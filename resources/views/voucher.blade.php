<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>TRINET SOLUTION - WiFi Login</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
html,body{min-height:100%;background:#eef3f7;font-family:Arial,Helvetica,sans-serif;color:#142033}
.page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{width:100%;max-width:420px;background:#fff;border:1px solid #d8dee8;box-shadow:0 16px 48px rgba(20,32,51,.14)}
.header{padding:28px 28px 22px;border-bottom:2px solid #142033;display:flex;gap:14px;align-items:center}
.mark{width:48px;height:48px;background:#142033;color:#fff;display:grid;place-items:center;font-size:13px;font-weight:900;flex:0 0 auto}
.brand{font-size:22px;font-weight:900;line-height:1}
.sub{font-size:11px;font-weight:800;color:#526173;letter-spacing:.1em;text-transform:uppercase;margin-top:5px}
.body{padding:28px}
.label{display:block;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#344054;margin-bottom:7px}
.input{width:100%;height:50px;padding:0 14px;font-size:17px;border:1.5px solid #b8c2d1;outline:none;background:#fbfdff;color:#142033}
.input:focus{border-color:#0b7a75;box-shadow:0 0 0 3px rgba(11,122,117,.12)}
.btn{display:block;width:100%;height:50px;margin-top:14px;background:#142033;color:#fff;border:0;font-size:13px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;text-decoration:none;text-align:center;line-height:50px}
.btn:hover{background:#0b7a75}
.btn:disabled{opacity:.5;cursor:not-allowed}
.buy{display:block;text-align:center;padding:13px;background:#f0faf9;border:1.5px solid #0b7a75;color:#075954;font-size:13px;font-weight:900;text-decoration:none;letter-spacing:.04em;text-transform:uppercase;margin-top:14px}
.buy:hover{background:#0b7a75;color:#fff}
.error{padding:11px 13px;margin-top:14px;background:#fff1f1;border-left:4px solid #c62828;color:#a81717;font-size:13px;font-weight:700;display:none}
.footer{padding:14px 28px;background:#f7f9fb;border-top:1px solid #e5e9f0;font-size:11px;color:#667085;text-align:center}
</style>
</head>
<body>
<main class="page">
  <div class="card">
    <div class="header">
      <div class="mark">TS</div>
      <div>
        <div class="brand">TRINET SOLUTION</div>
        <div class="sub">WiFi Hotspot &mdash; Tanzania</div>
      </div>
    </div>
    <div class="body">
      <form id="voucherForm" action="{{ $hotspot['link_login_only'] ?? '#' }}" method="post">
        @if($hotspot['link_login_only'])
          <input type="hidden" name="dst" value="{{ $hotspot['link_orig'] ?? 'http://www.google.com' }}">
          <input type="hidden" name="popup" value="true">
          <input type="hidden" name="password" value="">
        @endif
        <label class="label" for="voucher">Voucher Code</label>
        <input id="voucher" name="username" type="text" class="input" placeholder="Enter your voucher code" autocomplete="one-time-code" required>
        <button type="submit" class="btn">Connect</button>
        <div id="err" class="error"></div>
      </form>

      <a class="buy" href="{{ route('portal', ['mac'=>$hotspot['mac'],'ip'=>$hotspot['ip'],'link-login-only'=>$hotspot['link_login_only'],'link-orig'=>$hotspot['link_orig']]) }}">
        No voucher? Buy WiFi Now &rarr;
      </a>
    </div>
    <div class="footer">TRINET SOLUTION &mdash; Fast &amp; Affordable WiFi in Tanzania</div>
  </div>
</main>
<script>
(function(){
  var form = document.getElementById('voucherForm');
  var err = document.getElementById('err');
  if (!form) return;

  form.addEventListener('submit', function(e){
    var action = form.getAttribute('action');
    if (!action || action === '#') {
      e.preventDefault();
      err.textContent = 'Missing login endpoint. Please reconnect to the WiFi network and try again.';
      err.style.display = 'block';
      return;
    }
  });
})();
</script>
</body>
</html>
