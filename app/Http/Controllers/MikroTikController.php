<?php

namespace App\Http\Controllers;

use App\Models\MikroTikSetting;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MikroTikController extends Controller
{
    public function index()
    {
        $settings = MikroTikSetting::first();
        $users = [];
        $profiles = [];
        $error = null;

        if ($settings) {
            $service = new MikroTikService([
                'ip' => $settings->ip,
                'user' => $settings->username,
                'password' => $settings->password,
                'port' => $settings->port,
            ]);

            if ($service->connect()) {
                $users = $service->listHotspotUsers();
                $profiles = $service->listProfiles();
                $service->disconnect();
            } else {
                $error = 'Could not connect to MikroTik. Check IP/port/credentials.';
            }
        }

        return view('admin.mikrotik', compact('settings', 'users', 'profiles', 'error'));
    }

    public function storeSettings(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|ip',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
        ]);

        MikroTikSetting::updateOrCreate(['id' => 1], $data);

        return redirect()->route('mikrotik.index')->with('status', 'MikroTik settings saved.');
    }

    public function testConnection(Request $request)
    {
        $settings = MikroTikSetting::first();
        if (!$settings) {
            return response()->json(['success' => false, 'message' => 'No settings saved.']);
        }

        $service = new MikroTikService([
            'ip' => $settings->ip,
            'user' => $settings->username,
            'password' => $settings->password,
            'port' => $settings->port,
        ]);

        $connected = $service->connect();
        if ($connected) {
            $profiles = $service->listProfiles();
            $service->disconnect();
            return response()->json([
                'success' => true,
                'message' => 'Connected successfully.',
                'profiles' => $profiles,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Connection failed.']);
    }

    public function createVoucher(Request $request)
    {
        $settings = MikroTikSetting::first();
        if (!$settings) {
            return response()->json(['success' => false, 'message' => 'No MikroTik settings saved.']);
        }

        $data = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'profile' => 'required|string|max:255',
        ]);

        $service = new MikroTikService([
            'ip' => $settings->ip,
            'user' => $settings->username,
            'password' => $settings->password,
            'port' => $settings->port,
        ]);

        if (!$service->connect()) {
            return response()->json(['success' => false, 'message' => 'Could not connect to MikroTik.']);
        }

        $created = $service->createHotspotUser($data['username'], $data['password'] ?? '', $data['profile']);
        $service->disconnect();

        if ($created) {
            return response()->json(['success' => true, 'message' => 'Voucher created successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to create voucher. Check MikroTik logs.']);
    }

    public function destroyVoucher(Request $request, string $id)
    {
        $settings = MikroTikSetting::first();
        if (!$settings) {
            return response()->json(['success' => false, 'message' => 'No MikroTik settings saved.']);
        }

        $service = new MikroTikService([
            'ip' => $settings->ip,
            'user' => $settings->username,
            'password' => $settings->password,
            'port' => $settings->port,
        ]);

        if (!$service->connect()) {
            return response()->json(['success' => false, 'message' => 'Could not connect to MikroTik.']);
        }

        $removed = $service->removeHotspotUser($id);
        $service->disconnect();

        if ($removed) {
            return response()->json(['success' => true, 'message' => 'Voucher deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete voucher.']);
    }
}
