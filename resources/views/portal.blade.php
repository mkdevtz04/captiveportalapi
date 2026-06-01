<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TRINET SOLUTION - WiFi Payment Portal</title>
    <style>
        *{box-sizing:border-box}
        html,body{min-height:100%;margin:0}
        body{
            font-family:Arial,Helvetica,sans-serif;
            color:#142033;
            background:#eef3f7;
        }
        .page{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:22px;
        }
        .portal{
            width:min(1060px,100%);
            display:grid;
            grid-template-columns:minmax(0,1fr) 310px;
            background:#fff;
            border:1px solid #d8dee8;
            box-shadow:0 24px 60px rgba(20,32,51,.16);
            overflow:hidden;
        }
        .content{padding:36px 38px}
        .brand-row{
            display:flex;
            gap:15px;
            align-items:center;
            padding-bottom:22px;
            margin-bottom:24px;
            border-bottom:2px solid #142033;
        }
        .mark{
            width:52px;
            height:52px;
            display:grid;
            place-items:center;
            flex:0 0 auto;
            color:#fff;
            background:#142033;
            font-size:14px;
            font-weight:900;
        }
        .brand-title{
            margin:0;
            font-size:clamp(24px,4vw,38px);
            line-height:1;
            letter-spacing:0;
        }
        .brand-meta{
            margin:7px 0 0;
            color:#526173;
            font-size:12px;
            font-weight:800;
            letter-spacing:.1em;
            text-transform:uppercase;
        }
        .intro{
            max-width:680px;
            margin:0 0 24px;
            color:#526173;
            font-size:15px;
            line-height:1.65;
        }
        .layout{
            display:grid;
            grid-template-columns:minmax(0,1fr) 290px;
            gap:22px;
            align-items:start;
        }
        .payment-box,.packages{
            border:1px solid #d8dee8;
            background:#fff;
        }
        .payment-box{padding:24px}
        .section-title{
            margin:0 0 8px;
            font-size:21px;
            line-height:1.25;
        }
        .method-note{
            margin:0 0 18px;
            color:#667085;
            font-size:13px;
            line-height:1.5;
        }
        .error-msg{
            margin:0 0 15px;
            padding:12px 14px;
            color:#a81717;
            background:#fff1f1;
            border-left:4px solid #c62828;
            font-size:14px;
            font-weight:700;
            line-height:1.45;
            display:none;
            border-radius:4px;
        }
        .error-msg.show{display:block}
        .field{
            display:block;
            margin-bottom:15px;
        }
        .field span{
            display:block;
            margin-bottom:8px;
            color:#344054;
            font-size:12px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .field input{
            width:100%;
            height:52px;
            padding:0 15px;
            color:#142033;
            font-size:18px;
            border:1.5px solid #b8c2d1;
            outline:none;
            background:#fbfdff;
        }
        .field input:focus{
            border-color:#0b7a75;
            box-shadow:0 0 0 4px rgba(11,122,117,.12);
        }
        .field input::placeholder{
            color:#b8c2d1;
        }
        .phone-hint{
            display:flex;
            gap:6px;
            margin-top:8px;
            flex-wrap:wrap;
        }
        .hint-chip{
            font-size:11px;
            padding:4px 10px;
            background:#f3f4f6;
            color:#667085;
            border:1px solid #d8dee8;
            border-radius:4px;
        }
        .pkg-summary{
            background:#f9f9fb;
            border:1px solid #d8dee8;
            border-radius:8px;
            padding:14px 16px;
            margin-bottom:18px;
            font-size:13px;
            display:none;
        }
        .pkg-summary.show{display:block}
        .pkg-summary strong{color:#0b7a75;font-weight:700}
        .submit{
            width:100%;
            height:52px;
            margin-top:2px;
            color:#fff;
            background:#142033;
            border:0;
            cursor:pointer;
            font-size:14px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
            border-radius:4px;
        }
        .submit:hover{background:#0b7a75}
        .submit:disabled{opacity:.5;cursor:not-allowed}
        .pkg-header{
            padding:12px 16px;
            color:#fff;
            background:#142033;
            font-size:12px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .package{
            display:grid;
            grid-template-columns:1fr auto;
            gap:6px 12px;
            align-items:center;
            padding:15px 16px;
            border-top:1px solid #d8dee8;
            cursor:pointer;
            transition:background .2s;
        }
        .package:hover{background:#f9f9fb}
        .package.selected{background:#e8f4f4;border-left:4px solid #0b7a75;padding-left:12px}
        .pkg-name{font-size:15px;font-weight:900}
        .pkg-desc{margin-top:3px;color:#667085;font-size:13px}
        .price{
            color:#075954;
            font-size:15px;
            font-weight:900;
            white-space:nowrap;
        }
        .side{
            display:flex;
            flex-direction:column;
            gap:26px;
            padding:34px 24px;
            color:#fff;
            background:#101b28;
        }
        .side-title{
            margin:0;
            font-size:19px;
            font-weight:900;
            line-height:1.35;
        }
        .operators{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:8px;
        }
        .operator{
            min-height:64px;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:10px;
            color:#fff;
            font-size:12px;
            font-weight:900;
            text-align:center;
            border-radius:6px;
        }
        .vodacom{background:#e60000}
        .airtel{background:#d71920}
        .tigo{background:#004b9b}
        .divider{height:1px;background:rgba(255,255,255,.14)}
        .contacts{
            padding:16px;
            background:rgba(255,255,255,.07);
            border:1px solid rgba(255,255,255,.14);
            border-radius:6px;
        }
        .contacts-label{
            margin:0 0 10px;
            color:rgba(255,255,255,.6);
            font-size:12px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .contacts a{
            display:block;
            margin-top:8px;
            color:#fff;
            font-size:17px;
            font-weight:900;
            text-decoration:none;
        }
        .side-footer{
            margin:auto 0 0;
            color:rgba(255,255,255,.62);
            font-size:13px;
            line-height:1.6;
        }
        .modal-overlay{
            display:none;
            position:fixed;
            inset:0;
            background:rgba(20,32,51,.9);
            z-index:100;
            align-items:center;
            justify-content:center;
            padding:20px;
        }
        .modal-overlay.show{display:flex}
        .modal-box{
            background:#fff;
            border-radius:12px;
            padding:40px 32px;
            text-align:center;
            max-width:360px;
            width:100%;
            animation:slideUp .3s ease;
        }
        @keyframes slideUp{
            from{transform:translateY(30px);opacity:0}
            to{transform:translateY(0);opacity:1}
        }
        .modal-icon{font-size:56px;margin-bottom:16px;display:block}
        .modal-title{font-size:20px;font-weight:700;margin-bottom:8px;color:#142033}
        .modal-msg{color:#667085;font-size:14px;line-height:1.6;margin-bottom:24px}
        .spinner{
            width:48px;height:48px;
            border:3px solid #e8e8e8;
            border-top-color:#0b7a75;
            border-radius:50%;
            animation:spin .8s linear infinite;
            margin:0 auto 20px;
        }
        @keyframes spin{to{transform:rotate(360deg)}}
        .voucher-card{
            background:#f9f9fb;
            border:2px dashed #0b7a75;
            border-radius:8px;
            padding:20px;
            margin-bottom:20px;
            font-family:monospace;
            text-align:left;
        }
        .voucher-label{font-size:11px;color:#667085;letter-spacing:1px;text-transform:uppercase;margin-bottom:4px}
        .voucher-value{font-size:24px;font-weight:700;color:#0b7a75;letter-spacing:4px;word-break:break-all}
        .voucher-divider{height:1px;background:#d8dee8;margin:14px 0}
        .modal-btn{
            width:100%;
            padding:14px;
            background:#fff;
            border:1px solid #d8dee8;
            border-radius:6px;
            color:#667085;
            font-family:Arial,sans-serif;
            font-size:14px;
            cursor:pointer;
            transition:all .2s;
            margin-top:8px;
        }
        .modal-btn:hover{border-color:#0b7a75;color:#142033}
        .footer{text-align:center;color:#667085;font-size:12px;padding-top:20px;border-top:1px solid #d8dee8}
        .footer a{color:#0b7a75;text-decoration:none;font-weight:700}
        @media(max-width:900px){
            .portal,.layout{grid-template-columns:1fr}
        }
        @media(max-width:560px){
            .page{padding:0;align-items:stretch}
            .portal{min-height:100vh;border:0}
            .content,.side{padding:22px 16px}
            .brand-row{align-items:flex-start}
            .mark{width:46px;height:46px}
            .intro{font-size:14px}
            .payment-box{padding:20px}
        }
    </style>
</head>
<body>
<main class="page">
    <section class="portal" aria-label="TRINET SOLUTION payment portal">
        <div class="content">
            <div class="brand-row">
                <div class="mark" aria-hidden="true">TS</div>
                <div>
                    <h1 class="brand-title">TRINET SOLUTION</h1>
                    <p class="brand-meta">WiFi Hotspot - Tanzania</p>
                </div>
            </div>

            <p class="intro">
                Select your desired internet package, enter your phone number, and complete the payment via your preferred mobile network. Your access will be activated immediately after payment confirmation.
            </p>

            <div class="layout">
                <div class="packages" aria-label="Available packages">
                    <div class="pkg-header">Available Packages</div>
                    @foreach($packages as $key => $pkg)
                    <div class="package" onclick="selectPackage('{{ $key }}', {{ $pkg['price'] }}, '{{ $pkg['name'] }}', '{{ $pkg['duration'] }}')">
                        <div>
                            <div class="pkg-name">{{ $pkg['name'] }} {{ $pkg['icon'] }}</div>
                            <div class="pkg-desc">{{ $pkg['duration'] }} - {{ $pkg['speed'] }}</div>
                        </div>
                        <div class="price">{{ number_format($pkg['price']) }} TZS</div>
                    </div>
                    @endforeach
                </div>

                <div class="payment-box">
                    <h2 class="section-title">Purchase WiFi Access</h2>
                    <p class="method-note">Choose a package and enter your phone number to proceed with payment.</p>

                    <div class="error-msg" id="errorMsg"></div>

                    <div class="pkg-summary" id="pkgSummary">
                        Package: <strong id="summaryName"></strong> — <strong id="summaryPrice"></strong>
                    </div>

                    <label class="field">
                        <span>Phone Number</span>
                        <input type="tel" id="phoneInput" placeholder="e.g. 0712345678" required>
                        <div class="phone-hint">
                            <span class="hint-chip">Vodacom: 074/075/076</span>
                            <span class="hint-chip">Airtel: 068/069/078</span>
                            <span class="hint-chip">Tigo: 065/067/071</span>
                        </div>
                    </label>

                    <button class="submit" id="payBtn" onclick="initiatePayment()">Pay & Connect Now</button>
                    <p class="method-note" style="margin-top:16px;text-align:center;font-size:12px">
                        Payment is processed securely via PalmPesa
                    </p>
                </div>

            </div>

            <div class="footer">
                <p>© TRINET SOLUTION • <a href="https://www.trinetpay.online">trinetpay.online</a></p>
                <p style="margin-top:6px">📞 Support: +255 700 000 000</p>
            </div>
        </div>

        <aside class="side" aria-label="Payment methods and contact">
            <div class="contacts">
                <p class="contacts-label">Need Help?</p>
                <a href="tel:+255700000000">+255 700 000 000</a>
                <a href="tel:+255755000000">+255 755 000 000</a>
            </div>

            <p class="side-footer">
                TRINET SOLUTION provides reliable, fast, and affordable WiFi hotspot internet access across Tanzania. Connect instantly with our easy payment options.
            </p>

            <div class="divider"></div>

            <p class="side-title">Supported Payment Methods</p>

            <div class="operators" aria-label="Mobile network operators">
                <div class="operator vodacom">Vodacom</div>
                <div class="operator airtel">Airtel</div>
                <div class="operator tigo">Tigo</div>
            </div>
        </aside>
    </section>
</main>

<div class="modal-overlay" id="modal">
    <div class="modal-box" id="modalBox"></div>
</div>

<script>
let selectedPackage = null;
let selectedPrice = null;
let pollingInterval = null;
let currentTxnId = null;
let currentOrderId = null;

function selectPackage(key, price, name, duration) {
    selectedPackage = key;
    selectedPrice = price;
    document.querySelectorAll('.package').forEach(p => p.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('summaryName').textContent = name + ' (' + duration + ')';
    document.getElementById('summaryPrice').textContent = price.toLocaleString() + ' TZS';
    document.getElementById('pkgSummary').classList.add('show');
}

function showError(msg) {
    const el = document.getElementById('errorMsg');
    el.textContent = msg;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 5000);
}

function showModal(icon, title, msg, showSpinner = false) {
    const box = document.getElementById('modalBox');
    box.innerHTML = `
        <span class="modal-icon">${icon}</span>
        <div class="modal-title">${title}</div>
        <div class="modal-msg">${msg}</div>
        ${showSpinner ? '<div class="spinner"></div>' : ''}
    `;
    document.getElementById('modal').classList.add('show');
}

function showVoucher(username, password, pkgName) {
    const box = document.getElementById('modalBox');
    box.innerHTML = `
        <span class="modal-icon">✅</span>
        <div class="modal-title">Payment Successful!</div>
        <div class="modal-msg">Your WiFi access has been activated. Use the voucher below to connect.</div>
        <div class="voucher-card">
            <div class="voucher-label">Username</div>
            <div class="voucher-value">${username}</div>
            <div class="voucher-divider"></div>
            <div class="voucher-label">Password</div>
            <div class="voucher-value">${password}</div>
        </div>
        <p style="color:#667085;font-size:13px;margin:16px 0">Package: <strong>${pkgName}</strong></p>
        <button class="modal-btn" onclick="location.reload()">Close</button>
    `;
}

async function initiatePayment() {
    const phone = document.getElementById('phoneInput').value.trim();

    if (!selectedPackage) return showError('Please select a package!');
    if (!phone || phone.length < 10) return showError('Please enter a valid phone number!');

    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.textContent = 'Processing...';

    showModal('⏳', 'Processing...', 'Sending payment request to your phone...', true);

    try {
        const res = await fetch('/api/payment/initiate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone, package: selectedPackage })
        });

        const data = await res.json();

        if (data.status === 'success') {
            currentTxnId = data.transaction_id;
            currentOrderId = data.order_id;
            showModal('📱', 'Check Your Phone!', `A payment prompt has been sent to <strong>${phone}</strong>.<br><br>Confirm payment of <strong>${selectedPrice.toLocaleString()} TZS</strong>`, true);
            startPolling();
        } else {
            showModal('', 'Payment Failed', data.message || 'Something went wrong. Try again.');
            btn.disabled = false;
            btn.textContent = 'Pay & Connect Now';
        }
    } catch (e) {
        showModal('❌', 'Connection Error', 'Could not reach the server. Check your connection.');
        btn.disabled = false;
        btn.textContent = 'Pay & Connect Now';
    }
}

function startPolling() {
    let attempts = 0;
    pollingInterval = setInterval(async () => {
        attempts++;
        if (attempts > 40) {
            clearInterval(pollingInterval);
            showModal('⏱', 'Timeout', 'Payment not confirmed. If you paid, contact support.');
            return;
        }

        try {
            const res = await fetch(`/api/payment/status?transaction_id=${currentTxnId}&order_id=${currentOrderId}`);
            const data = await res.json();

            if (data.status === 'paid') {
                clearInterval(pollingInterval);
                showVoucher(data.voucher_user, data.voucher_pass, data.package);
            } else if (data.status === 'failed') {
                clearInterval(pollingInterval);
                showModal('❌', 'Payment Failed', 'Payment was declined. Please try again.');
            }
        } catch (e) { /* keep polling */ }
    }, 3000);
}

function closeModal() {
    document.getElementById('modal').classList.remove('show');
}
</script>
</body>
</html>
