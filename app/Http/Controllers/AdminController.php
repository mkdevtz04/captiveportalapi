<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\HotspotSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Overview cards
        $todayRevenue = Payment::where('status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('amount');

        $weekRevenue = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [now()->subWeek(), now()])
            ->sum('amount');

        $todayCount = Payment::where('status', 'paid')
            ->whereDate('paid_at', today())
            ->count();

        $weekCount = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [now()->subWeek(), now()])
            ->count();

        $activeSessions = HotspotSession::where('status', 'active')->count();

        $recentPayments = Payment::where('status', 'paid')
            ->latest('paid_at')
            ->limit(20)
            ->get();

        $topPackages = Payment::where('status', 'paid')
            ->select('package', DB::raw('count(*) as count'), DB::raw('sum(amount) as revenue'))
            ->groupBy('package')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.dashboard', compact(
            'todayRevenue', 'weekRevenue', 'todayCount', 'weekCount',
            'activeSessions', 'recentPayments', 'topPackages'
        ));
    }

    public function chartData(Request $request)
    {
        $type = $request->query('type', 'daily');

        if ($type === 'weekly') {
            $payments = Payment::where('status', 'paid')
                ->whereBetween('paid_at', [now()->subWeeks(6), now()])
                ->select(
                    DB::raw('YEARWEEK(paid_at, 1) as yearweek'),
                    DB::raw('MIN(paid_at) as week_start'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(amount) as revenue')
                )
                ->groupBy('yearweek')
                ->orderBy('yearweek')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->week_start->format('M d'),
                        'count' => (int) $item->count,
                        'revenue' => (float) $item->revenue,
                    ];
                })
                ->values()
                ->all();

            return response()->json($payments);
        }

        // Daily — last 14 days
        $payments = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [now()->subDays(13), now()])
            ->select(
                DB::raw('DATE(paid_at) as day'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as revenue')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => \Carbon\Carbon::parse($item->day)->format('M d'),
                    'count' => (int) $item->count,
                    'revenue' => (float) $item->revenue,
                ];
            })
            ->values()
            ->all();

        return response()->json($payments);
    }
}
