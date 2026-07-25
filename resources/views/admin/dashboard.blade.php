<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRINET SOLUTION - Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{min-height:100%;background:#eef3f7;font-family:Arial,Helvetica,sans-serif;color:#142033}
        .header{padding:24px 28px;background:#142033;color:#fff;display:flex;justify-content:space-between;align-items:center}
        .brand{font-size:18px;font-weight:900;letter-spacing:.04em}
        .sub{font-size:11px;color:#93c5fd;font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin-top:4px}
        .logout{padding:10px 16px;background:#c62828;color:#fff;border:0;font-size:12px;font-weight:800;cursor:pointer;text-transform:uppercase;letter-spacing:.04em}
        .logout:hover{background:#a81717}
        .container{padding:28px;max-width:1200px;margin:0 auto}
        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px}
        .card{background:#fff;border:1px solid #d8dee8;box-shadow:0 8px 24px rgba(20,32,51,.08);padding:22px 24px}
        .card-label{font-size:11px;font-weight:800;color:#526173;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px}
        .card-value{font-size:28px;font-weight:900;color:#142033}
        .card-sub{font-size:12px;color:#667085;margin-top:4px}
        .charts{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
        @media(max-width:768px){.charts{grid-template-columns:1fr}}
        .chart-card{background:#fff;border:1px solid #d8dee8;box-shadow:0 8px 24px rgba(20,32,51,.08);padding:22px 24px}
        .chart-title{font-size:14px;font-weight:900;margin-bottom:14px;color:#344054}
        table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #d8dee8;box-shadow:0 8px 24px rgba(20,32,51,.08)}
        th,td{padding:12px 14px;text-align:left;font-size:13px;border-bottom:1px solid #d8dee8}
        th{font-size:11px;font-weight:800;color:#526173;text-transform:uppercase;letter-spacing:.06em;background:#f7f9fb}
        tr:last-child td{border-bottom:0}
        .pill{padding:4px 10px;border-radius:999px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;display:inline-block}
        .pill.paid{background:#e6f4ea;color:#1e7e34}
        .pill.pending{background:#fff8e1;color:#b7770d}
        .pill.failed{background:#fff1f1;color:#a81717}
        .tabs{display:flex;gap:8px;margin-bottom:16px}
        .tab{padding:8px 16px;border:1.5px solid #d8dee8;background:#fff;color:#344054;font-size:13px;font-weight:800;cursor:pointer}
        .tab.active{background:#142033;color:#fff;border-color:#142033}
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">TRINET SOLUTION</div>
            <div class="sub">Admin Dashboard</div>
        </div>
        <button class="logout" onclick="if(confirm('Logout?'))location.href='/admin/logout'">Logout</button>
    </div>

    <div class="container">
        <div class="cards">
            <div class="card">
                <div class="card-label">Today's Revenue</div>
                <div class="card-value">{{ number_format($todayRevenue, 0) }} <small style="font-size:14px;color:#667085">TZS</small></div>
                <div class="card-sub">{{ $todayCount }} payments today</div>
            </div>
            <div class="card">
                <div class="card-label">Week Revenue</div>
                <div class="card-value">{{ number_format($weekRevenue, 0) }} <small style="font-size:14px;color:#667085">TZS</small></div>
                <div class="card-sub">{{ $weekCount }} payments this week</div>
            </div>
            <div class="card">
                <div class="card-label">Active Sessions</div>
                <div class="card-value">{{ $activeSessions }}</div>
                <div class="card-sub">Currently online</div>
            </div>
            <div class="card">
                <div class="card-label">Top Package</div>
                <div class="card-value" style="font-size:20px">
                    {{ $topPackages->first()->package ?? 'N/A' }}
                </div>
                <div class="card-sub">
                    {{ $topPackages->first()->count ?? 0 }} sales &middot; {{ number_format($topPackages->first()->revenue ?? 0, 0) }} TZS
                </div>
            </div>
        </div>

        <div class="charts">
            <div class="chart-card">
                <div class="chart-title">Payments Overview</div>
                <canvas id="mainChart" height="220"></canvas>
                <div class="tabs" style="margin-top:14px">
                    <button class="tab active" onclick="switchChart('daily', this)">Daily</button>
                    <button class="tab" onclick="switchChart('weekly', this)">Weekly</button>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Package Breakdown</div>
                <canvas id="packageChart" height="220"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">Recent Payments</div>
            <div style="overflow-x:auto">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Transaction</th>
                            <th>Phone</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td style="font-family:monospace;font-size:12px">{{ $payment->transaction_id }}</td>
                                <td>{{ $payment->phone }}</td>
                                <td>{{ $payment->package }}</td>
                                <td>{{ number_format($payment->amount, 0) }} TZS</td>
                                <td>
                                    <span class="pill {{ $payment->status }}">{{ $payment->status }}</span>
                                </td>
                                <td>{{ $payment->paid_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;color:#667085">No payments yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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

    async function switchChart(type, btn) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        const res = await fetch('/admin/api/chart?type=' + type);
        const data = await res.json();
        mainChart.data.labels = data.map(d => d.label);
        mainChart.data.datasets[0].data = data.map(d => d.count);
        mainChart.data.datasets[1].data = data.map(d => d.revenue);
        mainChart.update();
    }

    (async () => {
        const res = await fetch('/admin/api/chart?type=daily');
        const data = await res.json();
        mainChart.data.labels = data.map(d => d.label);
        mainChart.data.datasets[0].data = data.map(d => d.count);
        mainChart.data.datasets[1].data = data.map(d => d.revenue);
        mainChart.update();
    })();
    </script>
</body>
</html>
