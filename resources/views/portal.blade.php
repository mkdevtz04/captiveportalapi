<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TRINET SOLUTION — WiFi Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #050a0e;
            --surface: #0d1821;
            --surface2: #162130;
            --accent: #00e5ff;
            --accent2: #00ff88;
            --gold: #FFD700;
            --silver: #C0C0C0;
            --bronze: #CD7F32;
            --text: #e8f4f8;
            --muted: #7a9bb0;
            --border: rgba(0,229,255,0.15);
            --glow: 0 0 30px rgba(0,229,255,0.2);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(0,229,255,0.05) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(0,255,136,0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            padding: 20px 16px 40px;
            position: relative;
            z-index: 1;
        }

        /* Header */
        .header { text-align: center; padding: 30px 0 20px; }

        .logo-ring {
            width: 72px; height: 72px;
            border-radius: 50%;
            border: 2px solid var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: var(--glow);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%,100% { box-shadow: var(--glow); }
            50% { box-shadow: 0 0 50px rgba(0,229,255,0.4); }
        }

        .logo-ring svg { width: 36px; height: 36px; }

        .brand-name {
            font-size: 22px; font-weight: 800;
            letter-spacing: 2px; text-transform: uppercase;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-sub {
            font-size: 11px; color: var(--muted);
            letter-spacing: 3px; text-transform: uppercase; margin-top: 4px;
        }

        /* Operators */
        .operators {
            display: flex; justify-content: center;
            gap: 10px; margin: 20px 0; flex-wrap: wrap;
        }

        .op { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid; }
        .op-mpesa  { background: rgba(255,0,0,0.1); border-color: rgba(255,0,0,0.3); color: #ff6b6b; }
        .op-airtel { background: rgba(255,100,0,0.1); border-color: rgba(255,100,0,0.3); color: #ff8c42; }
        .op-tigo   { background: rgba(0,100,255,0.1); border-color: rgba(0,100,255,0.3); color: #5b9cf6; }
        .op-halo   { background: rgba(0,200,100,0.1); border-color: rgba(0,200,100,0.3); color: #4ade80; }

        /* Section title */
        .section-title {
            font-size: 11px; font-weight: 600;
            letter-spacing: 3px; text-transform: uppercase;
            color: var(--muted); margin-bottom: 14px;
        }

        /* Packages */
        .packages { display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px; }

        .pkg-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px; padding: 18px 20px;
            cursor: pointer; transition: all 0.25s ease;
            position: relative; overflow: hidden;
        }

        .pkg-card::before {
            content: ''; position: absolute;
            left: 0; top: 0; bottom: 0; width: 3px;
        }

        .pkg-card.bronze::before { background: var(--bronze); }
        .pkg-card.silver::before { background: var(--silver); }
        .pkg-card.gold::before   { background: var(--gold); }

        .pkg-card:hover, .pkg-card.selected {
            border-color: var(--accent);
            background: var(--surface2);
            transform: translateY(-2px);
            box-shadow: var(--glow);
        }

        .pkg-card.selected::after {
            content: '✓'; position: absolute;
            top: 14px; right: 16px;
            width: 22px; height: 22px;
            background: var(--accent); color: var(--bg);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }

        .pkg-top { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .pkg-icon { font-size: 22px; }
        .pkg-name { font-size: 16px; font-weight: 700; flex: 1; }

        .pkg-price {
            font-family: 'Space Mono', monospace;
            font-size: 18px; font-weight: 700; color: var(--accent);
        }

        .pkg-price span { font-size: 11px; color: var(--muted); font-family: 'Sora', sans-serif; }

        .pkg-details { display: flex; gap: 16px; }
        .pkg-stat { font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 4px; }
        .pkg-stat strong { color: var(--text); font-size: 12px; }

        /* Form */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px; padding: 24px 20px; margin-bottom: 16px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block; font-size: 11px; font-weight: 600;
            letter-spacing: 2px; text-transform: uppercase;
            color: var(--muted); margin-bottom: 8px;
        }

        .form-input {
            width: 100%; background: var(--bg);
            border: 1px solid var(--border); border-radius: 12px;
            padding: 14px 16px; color: var(--text);
            font-family: 'Sora', sans-serif; font-size: 15px;
            outline: none; transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0,229,255,0.08);
        }

        .form-input::placeholder { color: var(--muted); }

        .phone-hint { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }

        .hint-chip {
            font-size: 10px; padding: 3px 8px; border-radius: 10px;
            background: var(--surface2); color: var(--muted);
            border: 1px solid var(--border);
        }

        /* Selected summary */
        .selected-summary {
            display: none; background: rgba(0,229,255,0.05);
            border: 1px solid rgba(0,229,255,0.2);
            border-radius: 12px; padding: 12px 16px; margin-bottom: 18px;
            font-size: 13px; align-items: center; justify-content: space-between;
        }
        .selected-summary.show { display: flex; }
        .selected-summary strong { color: var(--accent); }

        /* Pay button */
        .pay-btn {
            width: 100%; padding: 18px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none; border-radius: 14px;
            color: var(--bg); font-family: 'Sora', sans-serif;
            font-size: 16px; font-weight: 800; letter-spacing: 1px;
            cursor: pointer; transition: all 0.2s;
        }

        .pay-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,229,255,0.3); }
        .pay-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* Error */
        .error-msg {
            background: rgba(255,80,80,0.1); border: 1px solid rgba(255,80,80,0.3);
            border-radius: 10px; padding: 12px 16px;
            color: #ff8080; font-size: 13px; margin-bottom: 16px; display: none;
        }

        /* Status overlay */
        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(5,10,14,0.95); z-index: 100;
            align-items: center; justify-content: center; padding: 20px;
        }

        .overlay.show { display: flex; }

        .status-box {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 24px; padding: 36px 28px;
            text-align: center; max-width: 340px; width: 100%;
            animation: slide-up 0.3s ease;
        }

        @keyframes slide-up {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        .status-icon { font-size: 56px; margin-bottom: 16px; display: block; }
        .status-title { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
        .status-msg { color: var(--muted); font-size: 14px; line-height: 1.6; margin-bottom: 24px; }

        .spinner {
            width: 48px; height: 48px;
            border: 3px solid var(--border); border-top-color: var(--accent);
            border-radius: 50%; animation: spin 0.8s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Voucher */
        .voucher-card {
            background: var(--bg); border: 1px dashed var(--accent);
            border-radius: 16px; padding: 20px; margin-bottom: 20px;
            font-family: 'Space Mono', monospace; text-align: left;
        }

        .voucher-label { font-size: 10px; color: var(--muted); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 4px; }
        .voucher-value { font-size: 24px; font-weight: 700; color: var(--accent); letter-spacing: 4px; }
        .voucher-divider { height: 1px; background: var(--border); margin: 14px 0; }

        /* Buttons */
        .close-btn {
            width: 100%; padding: 14px; background: transparent;
            border: 1px solid var(--border); border-radius: 12px;
            color: var(--muted); font-family: 'Sora', sans-serif;
            font-size: 14px; cursor: pointer; transition: all 0.2s; margin-top: 8px;
        }

        .close-btn:hover { border-color: var(--accent); color: var(--text); }

        /* Footer */
        .footer { text-align: center; color: var(--muted); font-size: 11px; padding-top: 20px; border-top: 1px solid var(--border); }
        .footer a { color: var(--accent); text-decoration: none; }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <div class="logo-ring">
            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 4C11.163 4 4 11.163 4 20s7.163 16 16 16 16-7.163 16-16S28.837 4 20 4z" stroke="#00e5ff" stroke-width="1.5"/>
                <path d="M12 20c0-4.418 3.582-8 8-8s8 3.582 8 8" stroke="#00e5ff" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M15.5 20c0-2.485 2.015-4.5 4.5-4.5s4.5 2.015 4.5 4.5" stroke="#00ff88" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="20" cy="20" r="2" fill="#00e5ff"/>
            </svg>
        </div>
        <div class="brand-name">TRINET SOLUTION</div>
        <div class="brand-sub">WiFi Hotspot Portal</div>
    </div>

    <!-- Operators -->
    <div class="operators">
        <span class="op op-mpesa">M-PESA</span>
        <span class="op op-airtel">AIRTEL</span>
        <span class="op op-tigo">TIGO PESA</span>
        <span class="op op-halo">HALOPESA</span>
    </div>

    <!-- Packages -->
    <div class="section-title">Choose Your Package</div>
    <div class="packages">
        @foreach($packages as $key => $pkg)
        <div class="pkg-card {{ $key }}"
             onclick="selectPackage('{{ $key }}', {{ $pkg['price'] }}, '{{ $pkg['name'] }}', '{{ $pkg['duration'] }}')">
            <div class="pkg-top">
                <span class="pkg-icon">{{ $pkg['icon'] }}</span>
                <span class="pkg-name">{{ $pkg['name'] }}</span>
                <div class="pkg-price">
                    {{ number_format($pkg['price']) }} <span>TZS</span>
                </div>
            </div>
            <div class="pkg-details">
                <div class="pkg-stat">⏱ <strong>{{ $pkg['duration'] }}</strong></div>
                <div class="pkg-stat">⚡ <strong>{{ $pkg['speed'] }}</strong></div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Form -->
    <div class="form-card">
        <div class="section-title">Your Details</div>

        <div class="selected-summary" id="selectedSummary">
            <span>Package: <strong id="summaryName"></strong></span>
            <strong id="summaryPrice"></strong>
        </div>

        <div class="error-msg" id="errorMsg"></div>

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-input" id="nameInput" placeholder="e.g. John Mwangi">
        </div>

        <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" class="form-input" id="phoneInput" placeholder="e.g. 0712345678">
            <div class="phone-hint">
                <span class="hint-chip">Vodacom: 074/075/076</span>
                <span class="hint-chip">Airtel: 068/069/078</span>
                <span class="hint-chip">Tigo: 065/067/071</span>
                <span class="hint-chip">Halo: 061/062</span>
            </div>
        </div>

        <button class="pay-btn" id="payBtn" onclick="initiatePayment()">
            PAY & CONNECT NOW
        </button>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>© TRINET SOLUTION &bull; <a href="https://www.trinetpay.online">trinetpay.online</a></p>
        <p style="margin-top:6px;">📞 Support: +255 XXX XXX XXX</p>
    </div>

</div>

<!-- Status Overlay -->
<div class="overlay" id="overlay">
    <div class="status-box" id="statusBox"></div>
</div>

<script>
let selectedPackage = null;
let selectedPrice   = null;
let pollingInterval = null;
let currentOrderId  = null;
let currentTxnId    = null;

function selectPackage(key, price, name, duration) {
    selectedPackage = key;
    selectedPrice   = price;
    document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('selected'));
    document.querySelector('.pkg-card.' + key).classList.add('selected');
    document.getElementById('summaryName').textContent  = name + ' — ' + duration;
    document.getElementById('summaryPrice').textContent = price.toLocaleString() + ' TZS';
    document.getElementById('selectedSummary').classList.add('show');
}

function showError(msg) {
    const el = document.getElementById('errorMsg');
    el.textContent   = msg;
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 5000);
}

async function initiatePayment() {
    const name  = document.getElementById('nameInput').value.trim();
    const phone = document.getElementById('phoneInput').value.trim();

    if (!selectedPackage) return showError('Please select a package!');
    if (!name)            return showError('Please enter your name!');
    if (!phone || phone.length < 10) return showError('Please enter a valid phone number!');

    const btn       = document.getElementById('payBtn');
    btn.disabled    = true;
    btn.textContent = 'Processing...';

    showLoading('Sending payment request to your phone...');

    try {
        const res = await fetch('/api/payment/initiate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone, name, package: selectedPackage })
        });

        const data = await res.json();

        if (data.status === 'success') {
            currentTxnId  = data.transaction_id;
            currentOrderId = data.order_id;
            showWaiting(phone);
            startPolling();
        } else {
            showStatusBox('❌', 'Payment Failed', data.message || 'Something went wrong. Try again.');
            resetBtn();
        }
    } catch (e) {
        showStatusBox('❌', 'Connection Error', 'Could not reach the server. Check your connection.');
        resetBtn();
    }
}

function startPolling() {
    let attempts = 0;
    pollingInterval = setInterval(async () => {
        attempts++;
        if (attempts > 40) {
            clearInterval(pollingInterval);
            showStatusBox('⏱', 'Timeout', 'Payment not confirmed. If you paid, contact support.');
            resetBtn();
            return;
        }

        try {
            const res  = await fetch(`/api/payment/status?transaction_id=${currentTxnId}&order_id=${currentOrderId}`);
            const data = await res.json();

            if (data.status === 'paid') {
                clearInterval(pollingInterval);
                showVoucher(data.voucher_user, data.voucher_pass, data.package);
                resetBtn();
            } else if (data.status === 'failed') {
                clearInterval(pollingInterval);
                showStatusBox('❌', 'Payment Failed', 'Payment was cancelled or failed. Please try again.');
                resetBtn();
            }
        } catch (e) { /* keep polling */ }
    }, 3000);
}

function showLoading(msg) {
    document.getElementById('statusBox').innerHTML = `
        <div class="spinner"></div>
        <div class="status-title">Processing...</div>
        <div class="status-msg">${msg}</div>
    `;
    document.getElementById('overlay').classList.add('show');
}

function showWaiting(phone) {
    document.getElementById('statusBox').innerHTML = `
        <span class="status-icon">📱</span>
        <div class="status-title">Check Your Phone!</div>
        <div class="status-msg">
            A payment prompt has been sent to <strong>${phone}</strong>.<br><br>
            Confirm payment of <strong>${selectedPrice.toLocaleString()} TZS</strong> to get connected.
        </div>
        <div class="spinner"></div>
    `;
}

function showStatusBox(icon, title, msg) {
    document.getElementById('statusBox').innerHTML = `
        <span class="status-icon">${icon}</span>
        <div class="status-title">${title}</div>
        <div class="status-msg">${msg}</div>
        <button class="close-btn" onclick="closeOverlay()">Close</button>
    `;
    document.getElementById('overlay').classList.add('show');
}

function showVoucher(user, pass, pkg) {
    document.getElementById('statusBox').innerHTML = `
        <span class="status-icon">🎉</span>
        <div class="status-title">You're Connected!</div>
        <div class="status-msg">Payment successful! Use these credentials on the login page.</div>
        <div class="voucher-card">
            <div class="voucher-label">Username</div>
            <div class="voucher-value">${user}</div>
            <div class="voucher-divider"></div>
            <div class="voucher-label">Password</div>
            <div class="voucher-value">${pass}</div>
        </div>
        <button class="close-btn" onclick="closeOverlay()">Done ✓</button>
    `;
}

function closeOverlay() {
    document.getElementById('overlay').classList.remove('show');
    clearInterval(pollingInterval);
}

function resetBtn() {
    const btn       = document.getElementById('payBtn');
    btn.disabled    = false;
    btn.textContent = 'PAY & CONNECT NOW';
}
</script>
</body>
</html>