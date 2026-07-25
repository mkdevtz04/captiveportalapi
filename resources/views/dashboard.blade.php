<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Today Revenue</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($todayRevenue, 0) }} <span class="text-sm text-gray-500">TZS</span></div>
                    <div class="text-xs text-gray-500 mt-1">{{ $todayCount }} payments today</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Week Revenue</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($weekRevenue, 0) }} <span class="text-sm text-gray-500">TZS</span></div>
                    <div class="text-xs text-gray-500 mt-1">{{ $weekCount }} payments this week</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Active Sessions</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ $activeSessions }}</div>
                    <div class="text-xs text-gray-500 mt-1">Currently online</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Top Package</div>
                    <div class="mt-2 text-lg font-bold text-gray-900">{{ $topPackages->first()->package ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $topPackages->first()->count ?? 0 }} sales &middot; {{ number_format($topPackages->first()->revenue ?? 0, 0) }} TZS</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-900 mb-4">Payments Overview</div>
                    <canvas id="mainChart" height="220"></canvas>
                    <div class="flex gap-2 mt-4">
                        <button id="tabDaily" class="px-3 py-1.5 text-xs font-bold bg-gray-900 text-white rounded">Daily</button>
                        <button id="tabWeekly" class="px-3 py-1.5 text-xs font-bold bg-white text-gray-700 border rounded">Weekly</button>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-900 mb-4">Package Breakdown</div>
                    <canvas id="packageChart" height="220"></canvas>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-sm font-bold text-gray-900 mb-4">Recent Payments</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">#</th>
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Transaction</th>
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Phone</th>
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Package</th>
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Amount</th>
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr class="border-b last:border-0">
                                    <td class="py-2 px-3">{{ $payment->id }}</td>
                                    <td class="py-2 px-3 font-mono text-xs">{{ $payment->transaction_id }}</td>
                                    <td class="py-2 px-3">{{ $payment->phone }}</td>
                                    <td class="py-2 px-3">{{ $payment->package }}</td>
                                    <td class="py-2 px-3">{{ number_format($payment->amount, 0) }} TZS</td>
                                    <td class="py-2 px-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold uppercase
                                            {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $payment->status }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">{{ $payment->paid_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-6 text-center text-gray-500">No payments yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    const ctx = document.getElementById('mainChart').getContext('2d');
    const packageCtx = document.getElementById('packageChart').getContext('2d');

    const mainChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: [], datasets: [
            { label: 'Payments', data: [], backgroundColor: '#142033', borderRadius: 4 },
            { label: 'Revenue (TZS)', data: [], backgroundColor: '#0b7a75', borderRadius: 4, yAxisID: 'y1' }
        ]},
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                y1: { position: 'right', beginAtZero: true, ticks: { callback: v => v.toLocaleString() + ' TZS' }, grid: { drawOnChartArea: false } }
            }
        }
    });

    const packageChart = new Chart(packageCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($topPackages->pluck('package')) !!},
            datasets: [{
                data: {!! json_encode($topPackages->pluck('count')) !!},
                backgroundColor: ['#142033', '#0b7a75', '#075954', '#c4b5fd', '#fdba74']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    async function loadDaily() {
        const res = await fetch('{{ route('admin.chart', ['type' => 'daily']) }}');
        const data = await res.json();
        mainChart.data.labels = data.map(d => d.label);
        mainChart.data.datasets[0].data = data.map(d => d.count);
        mainChart.data.datasets[1].data = data.map(d => d.revenue);
        mainChart.update();
    }

    async function loadWeekly() {
        const res = await fetch('{{ route('admin.chart', ['type' => 'weekly']) }}');
        const data = await res.json();
        mainChart.data.labels = data.map(d => d.label);
        mainChart.data.datasets[0].data = data.map(d => d.count);
        mainChart.data.datasets[1].data = data.map(d => d.revenue);
        mainChart.update();
    }

    document.getElementById('tabDaily').addEventListener('click', function () {
        this.classList.replace('bg-white','bg-gray-900'); this.classList.replace('text-gray-700','text-white');
        this.classList.replace('border','bg-gray-900');
        const weekly = document.getElementById('tabWeekly');
        weekly.classList.replace('bg-gray-900','bg-white'); weekly.classList.replace('text-white','text-gray-700');
        weekly.classList.add('border');
        loadDaily();
    });

    document.getElementById('tabWeekly').addEventListener('click', function () {
        this.classList.replace('bg-white','bg-gray-900'); this.classList.replace('text-gray-700','text-white');
        this.classList.replace('border','bg-gray-900');
        const daily = document.getElementById('tabDaily');
        daily.classList.replace('bg-gray-900','bg-white'); daily.classList.replace('text-white','text-gray-700');
        daily.classList.add('border');
        loadWeekly();
    });

    loadDaily();
    </script>
</x-app-layout>
