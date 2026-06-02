<?php
namespace App\Http\Controllers;

use App\Services\PalmPesaService;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $palmPesa;
    protected $mikrotik;

    public function __construct(
        PalmPesaService $palmPesa,
        MikrotikService $mikrotik
    ) {
        $this->palmPesa = $palmPesa;
        $this->mikrotik = $mikrotik;
    }

    // Show portal page
    public function index(Request $request)
    {
        $packages = config('package');
        $hotspot = [
            'mac'             => $request->query('mac'),
            'ip'              => $request->query('ip'),
            'username'        => $request->query('username'),
            'link_login_only' => $request->query('link-login-only'),
            'link_orig'       => $request->query('link-orig'),
            'error'           => $request->query('error'),
        ];

        return view('portal', compact('packages', 'hotspot'));
    }

    // Initiate payment
    public function initiate(Request $request)
    {
        $packageKeys = implode(',', array_keys(config('package')));

        $request->validate([
            'phone'   => 'required|string|min:10',
            'package' => 'required|in:' . $packageKeys,
            'mac' => 'nullable|string|max:32',
            'ip' => 'nullable|string|max:45',
            'link_login_only' => 'nullable|string|max:255',
            'link_orig' => 'nullable|string|max:255',
        ]);

        $name = 'Customer TRINET';

        $packages = config('package');
        $pkg      = $packages[$request->package];

        try {
            $result = $this->palmPesa->initiatePayment([
                'name'    => $name,
                'phone'   => $request->phone,
                'amount'  => $pkg['price'],
                'email'   => $request->email ?? 'customer@trinetpay.online',
            ]);

            // Store transaction in cache for 30 minutes
            Cache::put('txn_' . $result['transaction_id'], [
                'transaction_id' => $result['transaction_id'],
                'order_id'       => $result['order_id'],
                'phone'          => $request->phone,
                'name'           => $name,
                'package'        => $request->package,
                'profile'        => $pkg['profile'],
                'amount'         => $pkg['price'],
                'status'         => 'pending',
                'client_mac'     => $request->mac,
                'client_ip'      => $request->ip,
                'link_login_only'=> $request->link_login_only,
                'link_orig'      => $request->link_orig,
            ], now()->addMinutes(30));
            Cache::put('order_' . $result['order_id'], $result['transaction_id'], now()->addMinutes(30));

            return response()->json([
                'status'         => 'success',
                'message'        => 'Payment prompt sent to your phone!',
                'transaction_id' => $result['transaction_id'],
                'order_id'       => $result['order_id'],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // PalmPesa webhook callback
    public function callback(Request $request)
    {
        Log::info('PalmPesa Callback', $request->all());

        $orderId = $request->input('order_id');
        $status  = $request->input('payment_status');

        // Find transaction by order_id
        $transactionId = $request->input('transaction_id')
            ?? $this->findTransactionByOrderId($orderId);

        if (!$transactionId) {
            Log::warning('Callback: transaction not found', ['order_id' => $orderId]);
            return response()->json(['status' => 'not_found'], 404);
        }

        $transaction = Cache::get('txn_' . $transactionId);

        if (!$transaction) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if (strtoupper($status) === 'COMPLETED') {
            $this->unlockInternet($transaction, $transactionId);
        } else {
            $transaction['status'] = 'failed';
            Cache::put('txn_' . $transactionId, $transaction, now()->addHour());
        }

        return response()->json(['status' => 'received'], 200);
    }

    // Check payment status (polled by frontend)
    public function checkStatus(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $orderId       = $request->input('order_id');

        $transaction = Cache::get('txn_' . $transactionId);

        if (!$transaction) {
            return response()->json(['status' => 'not_found']);
        }

        // If still pending — poll PalmPesa directly
        if ($transaction['status'] === 'pending' && $orderId) {
            $result = $this->palmPesa->checkStatus($orderId);
            $paymentStatus = $result['data'][0]['payment_status'] ?? 'PENDING';

            if (strtoupper($paymentStatus) === 'COMPLETED') {
                $this->unlockInternet($transaction, $transactionId);
                $transaction = Cache::get('txn_' . $transactionId);
            }
        }

        if ($transaction['status'] === 'paid') {
            return response()->json([
                'status'       => 'paid',
                'wifi_token'   => $transaction['wifi_token'],
                'package'      => $transaction['package'],
                'login_url'    => $transaction['link_login_only'] ?? null,
                'dst'          => $transaction['link_orig'] ?? null,
            ]);
        }

        return response()->json(['status' => $transaction['status']]);
    }

    // Create MikroTik voucher and unlock internet
    private function unlockInternet(array $transaction, string $transactionId): void
    {
        $token = 'TN' . rand(100000, 999999);

        if ($this->mikrotik->connect()) {
            $created = $this->mikrotik->createHotspotUser(
                $token,
                $token,
                $transaction['profile']
            );
            $this->mikrotik->disconnect();

            if ($created) {
                $transaction['status']     = 'paid';
                $transaction['wifi_token'] = $token;
                Cache::put('txn_' . $transactionId, $transaction, now()->addDay());

                Log::info('Internet unlocked', [
                    'wifi_token' => $token,
                    'package'  => $transaction['package'],
                ]);
            }
        } else {
            Log::error('MikroTik connection failed for: ' . $transactionId);
        }
    }

    private function findTransactionByOrderId(?string $orderId): ?string
    {
        // Simple approach — order_id is stored in cache
        // For production use a database instead
        if (!$orderId) {
            return null;
        }

        return Cache::get('order_' . $orderId);
    }
}
