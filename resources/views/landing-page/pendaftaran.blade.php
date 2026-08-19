<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Mitra — Maurekap</title>
<meta name="description" content="Formulir pendaftaran mitra Maurekap. Isi data usaha, data PIC, dan upload bukti pembayaran untuk aktivasi akun.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<style>
/* ================= TOKEN & DASAR ================= */
:root{
  --blue:#2563EB; --blue-600:#1D4ED8; --blue-700:#1E40AF;
  --blue-50:#EFF6FF; --blue-100:#DBEAFE;
  --navy:#0F1B33; --ink:#0F172A; --muted:#5B6B84; --line:#E5EAF3;
  --orange:#F97316; --orange-600:#EA580C;
  --green:#16A34A; --green-50:#DCFCE7;
  --red:#EF4444;
  --bg:#F6F8FC; --card:#FFFFFF;
  --shadow:0 10px 30px rgba(15,27,51,.08);
  --shadow-sm:0 4px 14px rgba(15,27,51,.07);
  --font:'Plus Jakarta Sans',system-ui,sans-serif;
  --mono:'JetBrains Mono',ui-monospace,monospace;
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:var(--font);background:var(--bg);color:var(--ink);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
img{display:block;max-width:100%}
a{color:inherit;text-decoration:none}
ul{list-style:none}
.container{width:min(1160px,92%);margin-inline:auto}
h1,h2,h3{color:var(--navy);line-height:1.2;letter-spacing:-.01em}
::selection{background:var(--blue);color:#fff}
:focus-visible{outline:2px solid var(--blue);outline-offset:3px;border-radius:4px}
.mono{font-family:var(--mono)}

[data-rv]{opacity:0;transform:translateY(22px);transition:opacity .6s ease,transform .6s cubic-bezier(.2,.7,.2,1);transition-delay:var(--d,0s)}
[data-rv].on{opacity:1;transform:none}

/* ================= HEADER ================= */
header{position:sticky;top:0;z-index:60;background:#fff;border-bottom:1px solid var(--line)}
.head-in{display:flex;align-items:center;gap:18px;height:68px}
.logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:1.15rem;color:var(--navy)}
.logo-badge{width:34px;height:34px;border-radius:9px;background:var(--blue);display:grid;place-items:center;color:#fff;flex:none}
.head-right{margin-left:auto;display:flex;align-items:center;gap:16px}
.back-link{font-weight:600;font-size:.9rem;color:var(--muted);display:flex;align-items:center;gap:7px;transition:.2s}
.back-link:hover{color:var(--blue)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;font-family:var(--font);font-weight:700;font-size:.95rem;
  padding:14px 26px;border-radius:10px;border:2px solid transparent;cursor:pointer;transition:.22s;white-space:nowrap}
.btn .arr{transition:transform .2s}
.btn:hover .arr{transform:translateX(4px)}
.btn-blue{background:var(--blue);color:#fff;box-shadow:0 8px 20px rgba(37,99,235,.28)}
.btn-blue:hover{background:var(--blue-600);transform:translateY(-2px)}
.btn-outline{background:#fff;color:var(--blue);border-color:var(--blue)}
.btn-outline:hover{background:var(--blue-50)}
.btn-sm{padding:10px 18px;font-size:.86rem;border-width:1.5px}

/* ================= PAGE HEAD ================= */
.page-head{background:linear-gradient(180deg,#EAF1FF 0%,#F6F8FC 100%);padding:clamp(40px,6vw,64px) 0 clamp(34px,5vw,52px);position:relative;overflow:hidden}
.page-head::before{content:"";position:absolute;inset:0;background-image:radial-gradient(rgba(37,99,235,.08) 1px,transparent 1px);background-size:26px 26px;pointer-events:none}
.crumb{font-size:.8rem;font-weight:600;color:var(--muted);margin-bottom:14px}
.crumb a:hover{color:var(--blue)}
.crumb b{color:var(--blue)}
.page-head h1{font-size:clamp(1.7rem,1.2rem + 2.4vw,2.5rem);font-weight:800;margin-bottom:10px}
.page-head>p, .page-head .container>p{color:var(--muted);max-width:62ch;font-size:clamp(.92rem,.88rem + .2vw,1rem)}
.mini-steps{display:flex;gap:10px 26px;flex-wrap:wrap;margin-top:26px}
.mini-steps li{display:flex;align-items:center;gap:10px;font-size:.85rem;font-weight:700;color:#33415C}
.mini-steps .n{width:26px;height:26px;border-radius:50%;background:#fff;border:2px solid var(--blue);color:var(--blue);font-size:.75rem;font-weight:800;display:grid;place-items:center;flex:none}
.mini-steps li.done .n{background:var(--blue);color:#fff}
.mini-steps li + li::before{content:"";width:26px;height:2px;background:#C7D6F2;margin-right:16px;border-radius:2px}
@media (max-width:560px){.mini-steps li + li::before{display:none}}

/* ================= LAYOUT ================= */
.reg-wrap{padding:clamp(30px,5vw,54px) 0 clamp(60px,7vw,90px)}
.reg-grid{display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start}

/* ================= KARTU FORM ================= */
.reg-card{background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:clamp(24px,4vw,42px)}
.form-sec{display:flex;gap:14px;align-items:flex-start;margin:34px 0 20px;padding-top:30px;border-top:1px solid var(--line)}
.form-sec:first-child{margin-top:0;padding-top:0;border-top:0}
.sec-num{width:34px;height:34px;flex:none;border-radius:10px;background:var(--blue);color:#fff;font-weight:800;font-size:.95rem;display:grid;place-items:center;box-shadow:0 6px 14px rgba(37,99,235,.3)}
.form-sec h2{font-size:1.08rem;font-weight:800}
.form-sec p{font-size:.85rem;color:var(--muted)}

.f-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.span2{grid-column:1/-1}
.field{display:flex;flex-direction:column;gap:7px;min-width:0}
.field label{font-size:.85rem;font-weight:700;color:#28354C}
.req::after{content:" *";color:var(--red)}
.field input,.field textarea{border:1.5px solid var(--line);border-radius:10px;padding:12px 14px;font-family:var(--font);
  font-size:.92rem;color:var(--ink);background:#fff;width:100%;transition:border-color .2s,box-shadow .2s}
.field textarea{resize:vertical;min-height:88px}
.field input::placeholder,.field textarea::placeholder{color:#A8B3C5}
.field input:focus,.field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 4px rgba(37,99,235,.12)}
.field input:disabled{background:#F1F5F9;color:#8A97AB}
.hint{font-size:.74rem;color:var(--muted)}
.err{font-size:.74rem;color:var(--red);font-weight:600;display:none}
.field.invalid input,.field.invalid textarea{border-color:var(--red);box-shadow:0 0 0 4px rgba(239,68,68,.1)}
.field.invalid .err{display:block}
.same-row{display:flex;align-items:center;gap:9px;margin-top:2px}
.same-row input{width:16px;height:16px;accent-color:var(--blue);cursor:pointer}
.same-row label{font-size:.8rem;font-weight:600;color:var(--muted);cursor:pointer}

/* info pembayaran */
.pay-info{display:flex;gap:12px;align-items:flex-start;background:var(--blue-50);border:1px solid var(--blue-100);
  border-radius:12px;padding:14px 16px;margin-bottom:16px;font-size:.85rem;color:#28354C}
.pay-info svg{width:20px;height:20px;flex:none;color:var(--blue);margin-top:2px}
.pay-info b{color:var(--blue-700)}
.pay-info .mono{font-weight:700}

/* dropzone */
.drop{border:2px dashed #B9C8E8;background:#FAFCFF;border-radius:14px;padding:30px 20px;text-align:center;cursor:pointer;transition:.2s}
.drop:hover,.drop.drag{border-color:var(--blue);background:var(--blue-50)}
.drop-ic{width:52px;height:52px;margin:0 auto 14px;border-radius:50%;background:var(--blue-100);color:var(--blue);display:grid;place-items:center}
.drop-ic svg{width:24px;height:24px}
.drop b{display:block;font-size:.95rem;color:var(--navy);margin-bottom:4px}
.drop b span{color:var(--blue)}
.drop small{font-size:.76rem;color:var(--muted)}
.file-prev{display:none;gap:12px;align-items:center;border:1.5px solid var(--line);border-radius:12px;padding:10px 12px;margin-top:14px;background:#fff}
.file-prev.show{display:flex}
.file-prev img,.file-prev .pdf-ic{width:48px;height:48px;border-radius:9px;object-fit:cover;flex:none}
.file-prev .pdf-ic{background:var(--red);color:#fff;display:grid;place-items:center;font-size:.6rem;font-weight:800}
.file-prev .f-meta{min-width:0;flex:1}
.file-prev .f-meta b{display:block;font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.file-prev .f-meta span{font-size:.74rem;color:var(--muted)}
.file-prev .ok-ic{color:var(--green);flex:none}
.f-del{border:0;background:var(--bg);color:var(--muted);width:32px;height:32px;border-radius:8px;cursor:pointer;font-size:.9rem;flex:none;transition:.2s}
.f-del:hover{background:#FEE2E2;color:var(--red)}

/* terms & submit */
.terms{display:flex;gap:10px;align-items:flex-start;margin:26px 0 22px;font-size:.85rem;color:#33415C}
.terms input{width:17px;height:17px;margin-top:2px;accent-color:var(--blue);cursor:pointer;flex:none}
.terms a{color:var(--blue);font-weight:700;text-decoration:underline}
.terms.invalid{color:var(--red)}
.submit-row{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
.submit-row .btn{min-width:240px}
.secure-note{display:flex;align-items:center;gap:8px;font-size:.78rem;color:var(--muted)}
.secure-note svg{width:15px;height:15px;color:var(--green);flex:none}

/* ================= SIDEBAR ================= */
.reg-side{display:flex;flex-direction:column;gap:20px;position:sticky;top:88px}
.side-card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-sm);padding:24px;overflow:hidden;position:relative}
.side-card h3{font-size:1rem;font-weight:800;margin-bottom:6px}
.side-card .sub{font-size:.83rem;color:var(--muted)}
.plan{border-top:4px solid var(--blue)}
.plan .price{display:flex;align-items:baseline;gap:6px;margin:14px 0 4px}
.plan .price b{font-size:1.7rem;font-weight:800;color:var(--blue)}
.plan .price span{font-size:.85rem;color:var(--muted)}
.plan ul{margin-top:14px;display:grid;gap:10px}
.plan ul li{display:flex;gap:10px;font-size:.85rem;color:#33415C;align-items:flex-start}
.plan ul svg{width:16px;height:16px;flex:none;color:var(--green);margin-top:3px}
.plan .divider{border:0;border-top:1px dashed var(--line);margin:16px 0 12px}
.plan .total{display:flex;justify-content:space-between;font-size:.88rem;font-weight:700;color:var(--navy)}
.after ol{margin-top:14px;display:grid;gap:14px;counter-reset:st}
.after ol li{display:flex;gap:12px;font-size:.85rem;color:#33415C;counter-increment:st}
.after ol li::before{content:counter(st);width:24px;height:24px;flex:none;border-radius:50%;background:var(--blue-50);color:var(--blue);font-size:.72rem;font-weight:800;display:grid;place-items:center;border:1.5px solid var(--blue-100)}
.help-card{background:var(--navy);border-color:var(--navy)}
.help-card h3{color:#fff}
.help-card .sub{color:#9FB0C9}
.help-card .btn{width:100%;margin-top:16px}

/* ================= SUKSES ================= */
.success{max-width:600px;margin:0 auto;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:var(--shadow);padding:clamp(34px,6vw,54px);text-align:center}
.ok-big{width:84px;height:84px;margin:0 auto 22px}
.ok-big circle{stroke:var(--green);stroke-width:2.5;fill:var(--green-50);stroke-dasharray:260;stroke-dashoffset:260;animation:draw 1s .1s ease forwards}
.ok-big path{stroke:var(--green);stroke-width:4;fill:none;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:60;stroke-dashoffset:60;animation:draw .6s .8s ease forwards}
@keyframes draw{to{stroke-dashoffset:0}}
.success h2{font-size:clamp(1.4rem,1.1rem + 1.6vw,1.9rem);font-weight:800;margin-bottom:10px}
.success>p{color:var(--muted);font-size:.95rem;max-width:44ch;margin-inline:auto}
.reg-code{margin:24px auto;padding:14px 22px;background:var(--blue-50);border:1.5px dashed var(--blue);border-radius:12px;display:inline-block}
.reg-code small{display:block;font-size:.68rem;letter-spacing:.14em;color:var(--muted);font-weight:700;margin-bottom:4px}
.reg-code b{font-family:var(--mono);font-size:1.25rem;color:var(--blue-700);letter-spacing:.06em}
.next-list{margin:8px auto 26px;display:grid;gap:10px;text-align:left;max-width:400px}
.next-list li{display:flex;gap:10px;font-size:.87rem;color:#33415C}
.next-list svg{width:17px;height:17px;flex:none;color:var(--green);margin-top:2px}
.success .btn-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}

/* ================= FOOTER MINI ================= */
.foot-mini{background:var(--navy);color:#9FB0C9;padding:22px 0;font-size:.82rem}
.foot-mini .container{display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;align-items:center}
.foot-mini a:hover{color:#fff}

/* ================= RESPONSIVE ================= */
@media (max-width:1024px){
  .reg-grid{grid-template-columns:1fr}
  .reg-side{position:static;display:grid;grid-template-columns:1fr 1fr;align-items:stretch}
  .help-card{grid-column:1/-1}
}
@media (max-width:720px){
  .reg-side{grid-template-columns:1fr}
}
@media (max-width:640px){
  .f-grid{grid-template-columns:1fr}
  .submit-row .btn{width:100%}
  .back-link span{display:none}
}
@media (max-width:380px){
  .reg-card{padding:20px 16px}
  .head-in{height:60px}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation:none!important;transition:none!important}
  html{scroll-behavior:auto}
  [data-rv]{opacity:1;transform:none}
  .ok-big circle,.ok-big path{stroke-dashoffset:0}
}
</style>
</head>
<body>

<!-- ============ HEADER ============ -->
<header>
  <div class="container head-in">
    <a class="logo" href="index.html">
      <span class="">
        <!-- Logo Ku SVG -->
        <img src="{{ asset('img/logo/maurekap-icon-hd-transparent.png') }}" alt="Logo Maurekap" width="54" height="54" style="display:block;">

      </span>
      MAUREKAP
    </a>
    <div class="head-right">
      <a class="back-link" href="index.html"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5m0 0l6-6m-6 6l6 6"/></svg><span>Kembali ke Beranda</span></a>
      <a class="btn btn-outline btn-sm" href="#">Butuh Bantuan?</a>
    </div>
  </div>
</header>

<!-- ============ PAGE HEAD ============ -->
<section class="page-head">
  <div class="container">
    <p class="crumb" data-rv><a href="index.html">Beranda</a> &nbsp;/&nbsp; <b>Pendaftaran Mitra</b></p>
    <h1 data-rv style="--d:.06s">Formulir Pendaftaran Mitra</h1>
    <p data-rv style="--d:.12s">Lengkapi data di bawah untuk aktivasi akun Maurekap Anda. Pastikan data sesuai dengan bukti pembayaran yang dikirim.</p>
    <ul class="mini-steps" data-rv style="--d:.18s">
      <li class="done"><span class="n">✓</span> Pembayaran</li>
      <li class="done"><span class="n">2</span> Isi Formulir</li>
      <li><span class="n">3</span> Verifikasi 1×24 jam</li>
    </ul>
  </div>
</section>

<!-- ============ FORM + SIDEBAR ============ -->
<main class="reg-wrap">
  <div class="container reg-grid">

    <!-- ===== FORM ===== -->
    <form id="regForm" class="reg-card" novalidate data-rv enctype="multipart/form-data" method="POST" action="{{ route('pendaftaran-tenant.store') }}">
      @csrf

      <!-- 1. DATA USAHA -->
      <div class="form-sec">
        <span class="sec-num">1</span>
        <div><h2>Data Usaha</h2><p>Informasi utama usaha ekspedisi Anda.</p></div>
      </div>
      <div class="f-grid">
        <div class="field">
          <label for="Nama" class="req">Nama</label>
          <input id="Nama" name="Nama" type="text" placeholder="cth: Ekspedisi Jaya Makmur" data-validate="required" autocomplete="organization" value="{{ old('Nama') }}">
          <span class="err">@error('Nama'){{ $message }}@else Nama wajib diisi.@enderror</span>
        </div>
        <div class="field">
          <label for="Email" class="req">Email</label>
          <input id="Email" name="Email" type="email" placeholder="nama@usaha.co.id" data-validate="email" autocomplete="email" value="{{ old('Email') }}">
          <span class="err">@error('Email'){{ $message }}@else Masukkan alamat email yang valid.@enderror</span>
        </div>
        <div class="field">
          <label for="Telepon" class="req">Telepon</label>
          <input id="Telepon" name="Telepon" type="tel" placeholder="cth: 081234567890" data-validate="phone" autocomplete="tel" value="{{ old('Telepon') }}">
          <span class="err">@error('Telepon'){{ $message }}@else Nomor telepon tidak valid (min. 9 digit).@enderror</span>
        </div>
        <div class="field span2">
          <label for="Alamat" class="req">Alamat</label>
          <textarea id="Alamat" name="Alamat" rows="3" placeholder="Nama jalan, nomor, kecamatan, kota, kode pos" data-validate="required" autocomplete="street-address">{{ old('Alamat') }}</textarea>
          <span class="err">@error('Alamat'){{ $message }}@else Alamat wajib diisi.@enderror</span>
        </div>
      </div>

      <!-- 2. DATA PIC -->
      <div class="form-sec">
        <span class="sec-num">2</span>
        <div><h2>Data PIC / Kontak Person Utama</h2><p>Orang yang kami hubungi untuk aktivasi &amp; onboarding.</p></div>
      </div>
      <div class="f-grid">
        <div class="field">
          <label for="NamaPIC" class="req">Nama PIC</label>
          <input id="NamaPIC" name="NamaPIC" type="text" placeholder="cth: Budi Santoso" data-validate="required" autocomplete="name" value="{{ old('NamaPIC') }}">
          <span class="err">@error('NamaPIC'){{ $message }}@else Nama PIC wajib diisi.@enderror</span>
        </div>
        <div class="field">
          <label for="EmailPIC" class="req">Email PIC</label>
          <input id="EmailPIC" name="EmailPIC" type="email" placeholder="budi@usaha.co.id" data-validate="email" autocomplete="email" value="{{ old('EmailPIC') }}">
          <span class="err">@error('EmailPIC'){{ $message }}@else Masukkan alamat email yang valid.@enderror</span>
        </div>
        <div class="field">
          <label for="TeleponPIC" class="req">Telepon PIC</label>
          <input id="TeleponPIC" name="TeleponPIC" type="tel" placeholder="cth: 081234567890" data-validate="phone" autocomplete="tel" value="{{ old('TeleponPIC') }}">
          <span class="err">@error('TeleponPIC'){{ $message }}@else Nomor telepon tidak valid (min. 9 digit).@enderror</span>
        </div>
        <div class="field">
          <div class="same-row">
            <input id="sameAddr" type="checkbox">
            <label for="sameAddr">Alamat PIC sama dengan alamat usaha</label>
          </div>
        </div>
        <div class="field span2">
          <label for="AlamatPIC" class="req">Alamat PIC</label>
          <textarea id="AlamatPIC" name="AlamatPIC" rows="3" placeholder="Alamat lengkap kontak person" data-validate="required">{{ old('AlamatPIC') }}</textarea>
          <span class="err">@error('AlamatPIC'){{ $message }}@else Alamat PIC wajib diisi.@enderror</span>
        </div>
      </div>

      <!-- 3. BUKTI BAYAR -->
      <div class="form-sec">
        <span class="sec-num">3</span>
        <div><h2>Bukti Pembayaran</h2><p>Upload bukti transfer / QRIS sesuai nominal paket.</p></div>
      </div>

      <div class="pay-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><path d="M7 15h4"/></svg>
        <p>Transfer ke <b>BCA <span class="mono">123-456-7890</span></b> a.n. <b>Maurekap Teknologi</b> — atau scan QRIS pada invoice Anda, lalu upload buktinya di bawah.</p>
      </div>

      <div class="field" id="fileField">
        <label for="buktibayar" class="req" style="margin-bottom:.5em;display:block;">Upload Bukti Pembayaran</label>
        <div class="drop" id="drop" role="button" tabindex="0" aria-label="Upload bukti pembayaran" onclick="document.getElementById('buktibayar').click();" onkeydown="if(event.key==='Enter'){document.getElementById('buktibayar').click();}">
          <div class="drop-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V5m0 0l-4 4m4-4l4 4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg></div>
          <b>Klik untuk pilih file <span>atau seret ke sini</span></b>
          <small>JPG, PNG, atau PDF · maks. 5 MB</small>
        </div>
        <input id="buktibayar"
               name="BuktiPembayaran"
               type="file"
               accept="image/jpeg,image/png,image/webp,application/pdf"
               onchange="previewBuktiBayar(this)"
               style="display:none">

        <div class="file-prev" id="filePrev" style="display: none;">
          <img id="fThumb" alt="Pratinjau bukti bayar" hidden>
          <span class="pdf-ic" id="fPdf" hidden>PDF</span>
          <div class="f-meta"><b id="fName">—</b><span id="fSize">—</span></div>
          <svg class="ok-ic" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          <button type="button" class="f-del" id="fDel" aria-label="Hapus file" onclick="hapusBuktiBayar()">✕</button>
        </div>
        <span class="err" id="buktiErr">@error('buktibayar'){{ $message }}@else Bukti pembayaran wajib diupload (JPG/PNG/PDF, maks. 5 MB).@enderror</span>
      </div>

      <div class="terms" id="termsRow">
        <input id="terms" name="terms" type="checkbox" {{ old('terms') ? 'checked' : '' }}>
        <label for="terms">Saya menyatakan data di atas benar dan menyetujui <a href="#">Syarat &amp; Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> Maurekap.</label>
      </div>

      <div class="submit-row">
        <button type="submit" class="btn btn-blue">Kirim Pendaftaran <span class="arr">→</span></button>
        <span class="secure-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Data Anda aman &amp; terenkripsi</span>
      </div>
    </form>

    <!-- ===== SIDEBAR ===== -->
    <aside class="reg-side">
      <div class="side-card plan" data-rv style="--d:.08s">
        <h3>Spesifikasi</h3>
        <p class="sub">Paket paling populer untuk agen ekspedisi berkembang.</p>
        <div class="price"><b>Rp 149.000</b><span>/ bulan</span></div>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Transaksi &amp; seller tanpa batas</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Upload bukti Transfer &amp; QRIS</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Rekap harian &amp; laporan ekspor</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Multi-admin hingga 5 pengguna</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Support WhatsApp 7 hari</li>
        </ul>
        <hr class="divider">
        <div class="total"><span>Total Tagihan</span><span class="mono">Rp 149.000</span></div>
      </div>

      <div class="side-card after" data-rv style="--d:.16s">
        <h3>Setelah Mendaftar</h3>
        <p class="sub">Prosesnya cepat dan dibantu tim kami.</p>
        <ol>
          <li>Tim kami memverifikasi data &amp; bukti bayar Anda (maks. 1×24 jam kerja).</li>
          <li>Akun diaktifkan, kredensial login dikirim ke email PIC.</li>
          <li>Sesi onboarding gratis 30 menit via Zoom/WhatsApp — tim langsung mahir.</li>
        </ol>
      </div>

      <div class="side-card help-card" data-rv style="--d:.24s">
        <h3>Butuh Bantuan?</h3>
        <p class="sub">Tim support siap membantu Senin–Sabtu, 08.00–20.00 WIB.</p>
        <a class="btn btn-blue" href="#">Chat via WhatsApp</a>
      </div>
    </aside>
  </div>

  <!-- ===== PANEL SUKSES ===== -->
  <div class="container" id="successBox" hidden>
    <div class="success">
      <svg class="ok-big" viewBox="0 0 84 84"><circle cx="42" cy="42" r="40"/><path d="M27 43.5l10 10 20-22"/></svg>
      <h2>Pendaftaran Terkirim!</h2>
      <p>Terima kasih. Simpan nomor registrasi Anda di bawah ini untuk pengecekan status:</p>
      <div class="reg-code"><small>NOMOR REGISTRASI</small><b id="regCode">MRK-2026-0000</b></div>
      <ul class="next-list">
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Verifikasi data &amp; bukti bayar maks. 1×24 jam kerja.</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Kredensial login dikirim ke email PIC.</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>Tim onboarding kami akan menghubungi WhatsApp PIC.</li>
      </ul>
      <div class="btn-row">
        <a class="btn btn-blue" href="index.html">Kembali ke Beranda</a>
        <a class="btn btn-outline" href="#">Cek Status Pendaftaran</a>
      </div>
    </div>
  </div>
  <!-- SweetAlert2 CDN (or you can use your own assets if you wish) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Konfirmasi SweetAlert sebelum submit form
    document.addEventListener('DOMContentLoaded', function() {
      var regForm = document.getElementById('regForm');
      if (regForm) {
        regForm.addEventListener('submit', function(e){
          e.preventDefault();
          Swal.fire({
            title: 'Konfirmasi Kirim Data?',
            text: "Pastikan data sudah benar sebelum disubmit.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
              regForm.submit();
            }
          });
        }, false);
      }
    });

    // Tampilkan panel sukses jika berhasil daftar & kode registrasi ada di flash session
    document.addEventListener('DOMContentLoaded', function() {
      @if(session('success'))
        // Ambil data dari session
        var kodeReg = @json(session('success.kode') ?? '');
        // Tampilkan box sukses, sembunyikan form (asumsikan ada elemen formBox)
        var successBox = document.getElementById('successBox');
        var regCode = document.getElementById('regCode');
        var formBox = document.getElementById('formBox');
        if (successBox && regCode) {
          successBox.hidden = false;
          regCode.textContent = kodeReg || '—';
        }
        if (formBox) {
          formBox.style.display = 'none';
        }
        // SweetAlert sukses!
        Swal.fire({
          icon: 'success',
          title: 'Pendaftaran tersimpan!',
          text: 'Terimakasih, data Anda berhasil terkirim. Simpan nomor registrasi Anda.',
          timer: 3500,
          showConfirmButton: false
        });
        // Scroll ke sukses panel
        setTimeout(function(){
          successBox.scrollIntoView({behavior: 'smooth'});
        }, 300);
      @endif
    });
  </script>

  <script>
    // Preview bukti bayar di UI (file field)
    function previewBuktiBayar(input) {
      var fileField = document.getElementById('fileField');
      var filePrev = document.getElementById('filePrev');
      var thumb = document.getElementById('fThumb');
      var pdfIc = document.getElementById('fPdf');
      var fName = document.getElementById('fName');
      var fSize = document.getElementById('fSize');
      var buktiErr = document.getElementById('buktiErr');
      var file = input.files && input.files[0] ? input.files[0] : null;
      if (!file) {
        filePrev.style.display = 'none';
        thumb.hidden = true;
        pdfIc.hidden = true;
        fName.textContent = '—';
        fSize.textContent = '—';
        buktiErr.textContent = 'Bukti pembayaran wajib diupload (JPG/PNG/PDF, maks. 5 MB).';
        fileField.classList.remove('invalid');
        return;
      }
      var OK_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf'
      ];
      var MAX = 5 * 1024 * 1024;
      if (OK_TYPES.indexOf(file.type) === -1 || file.size > MAX) {
        filePrev.style.display = 'none';
        buktiErr.textContent = 'Format atau ukuran file tidak sesuai (JPG/PNG/PDF · maks. 5 MB).';
        fileField.classList.add('invalid');
        Swal.fire({
          icon: 'error',
          title: 'File tidak valid!',
          text: 'Format atau ukuran file tidak sesuai (JPG/PNG/PDF · maks. 5 MB).'
        });
        return;
      }
      fileField.classList.remove('invalid');
      filePrev.style.display = '';
      fName.textContent = file.name;
      fSize.textContent = (file.size < 1024*1024 ? (file.size/1024).toFixed(0)+' KB' : (file.size/1024/1024).toFixed(2)+' MB') + ' · siap diverifikasi';
      if (file.type === 'application/pdf') {
        thumb.hidden = true;
        pdfIc.hidden = false;
      } else {
        // Untuk image
        var reader = new FileReader();
        reader.onload = function (e) {
          thumb.src = e.target.result;
          thumb.hidden = false;
          pdfIc.hidden = true;
        };
        reader.readAsDataURL(file);
      }
      buktiErr.textContent = '';
    }

    // Hapus file preview & file input
    function hapusBuktiBayar() {
      var fileInput = document.getElementById('buktibayar');
      Swal.fire({
        title: 'Hapus file?',
        text: "File bukti pembayaran ini akan dihapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa'
      }).then((result) => {
        if (result.isConfirmed) {
          fileInput.value = '';
          previewBuktiBayar(fileInput);
        }
      });
    }

    // Drag and Drop untuk area drop
    (function(){
      var drop = document.getElementById('drop');
      var fileInput = document.getElementById('buktibayar');
      if (!drop || !fileInput) return;

      drop.addEventListener('dragover', function(e) {
        e.preventDefault();
        drop.classList.add('hover');
      });
      drop.addEventListener('dragleave', function(e) {
        drop.classList.remove('hover');
      });
      drop.addEventListener('drop', function(e) {
        e.preventDefault();
        drop.classList.remove('hover');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
          fileInput.files = e.dataTransfer.files;
          previewBuktiBayar(fileInput);
        }
      });
    })();

    // Init preview if value already exists (for old input on validation error)
    window.addEventListener('DOMContentLoaded', function () {
      var fileInput = document.getElementById('buktibayar');
      if (fileInput && fileInput.files && fileInput.files.length) {
        previewBuktiBayar(fileInput);
      }
    });
  </script>
</main>

<!-- ============ FOOTER MINI ============ -->
<footer class="foot-mini">
  <div class="container">
    <span>© 2026 Maurekap — PT Maurekap Teknologi Logistik</span>
    <span><a href="#">Kebijakan Privasi</a> &nbsp;·&nbsp; <a href="#">Syarat &amp; Ketentuan</a> &nbsp;·&nbsp; support@maurekap.id</span>
  </div>
</footer>

<script>
(function(){
  var $ = function(s){ return document.querySelector(s); };

  /* reveal on scroll */
  var io = new IntersectionObserver(function(es){
    es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('on'); io.unobserve(e.target);} });
  },{threshold:.1});
  document.querySelectorAll('[data-rv]').forEach(function(el){ io.observe(el); });

  /* ---- filter karakter telepon ---- */
  ['telepon','teleponpic'].forEach(function(id){
    var el = document.getElementById(id);
    el.addEventListener('input', function(){ el.value = el.value.replace(/[^\d+\-\s]/g,''); });
  });

  /* ---- alamat PIC sama dengan usaha ---- */
  var same = $('#sameAddr'), alamat = $('#alamat'), alamatPic = $('#alamatpic');
  same.addEventListener('change', function(){
    if(same.checked){ alamatPic.value = alamat.value; alamatPic.disabled = true; }
    else{ alamatPic.disabled = false; }
  });
  alamat.addEventListener('input', function(){ if(same.checked) alamatPic.value = alamat.value; });

  /* ---- upload bukti bayar ---- */
  var drop = $('#drop'), fileInput = $('#buktibayar'), prev = $('#filePrev'),
      thumb = $('#fThumb'), pdfIc = $('#fPdf'), fName = $('#fName'), fSize = $('#fSize'),
      fileField = $('#fileField'), storedFile = null;
  var OK_TYPES = ['image/jpeg','image/png','image/webp','application/pdf'];
  var MAX = 5 * 1024 * 1024;

  function fmtSize(b){ return b < 1024*1024 ? (b/1024).toFixed(0)+' KB' : (b/1024/1024).toFixed(2)+' MB'; }
  function setFile(f){
    if(!f) return;
    if(OK_TYPES.indexOf(f.type) === -1 || f.size > MAX){
      fileField.classList.add('invalid'); return;
    }
    storedFile = f;
    fileField.classList.remove('invalid');
    fName.textContent = f.name;
    fSize.textContent = fmtSize(f.size) + ' · siap diverifikasi';
    var isPdf = f.type === 'application/pdf';
    pdfIc.hidden = !isPdf; thumb.hidden = isPdf;
    if(!isPdf){ thumb.src = URL.createObjectURL(f); }
    prev.classList.add('show');
    drop.style.display = 'none';
  }
  function clearFile(){
    storedFile = null; fileInput.value = '';
    prev.classList.remove('show'); drop.style.display = '';
  }
  drop.addEventListener('click', function(){ fileInput.click(); });
  drop.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); fileInput.click(); } });
  ['dragover','dragenter'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.add('drag'); }); });
  ['dragleave','drop'].forEach(function(ev){ drop.addEventListener(ev, function(e){ e.preventDefault(); drop.classList.remove('drag'); }); });
  drop.addEventListener('drop', function(e){ setFile(e.dataTransfer.files[0]); });
  fileInput.addEventListener('change', function(){ setFile(fileInput.files[0]); });
  $('#fDel').addEventListener('click', clearFile);

  /* ---- validasi & submit ---- */
  var form = $('#regForm');
  function checkField(input){
    var rule = input.dataset.validate, v = input.value.trim(), ok = true;
    if(rule === 'required') ok = v.length > 0;
    if(rule === 'email') ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);
    if(rule === 'phone') ok = /^[0-9+\-\s]{9,16}$/.test(v);
    input.closest('.field').classList.toggle('invalid', !ok);
    return ok;
  }
  form.querySelectorAll('[data-validate]').forEach(function(inp){
    inp.addEventListener('input', function(){ inp.closest('.field').classList.remove('invalid'); });
    inp.addEventListener('blur', function(){ if(inp.value.trim() !== '') checkField(inp); });
  });

  form.addEventListener('submit', function(e){
    e.preventDefault();
    var valid = true;
    form.querySelectorAll('[data-validate]').forEach(function(inp){ if(!checkField(inp)) valid = false; });
    if(!storedFile){ fileField.classList.add('invalid'); valid = false; }
    var termsOk = $('#terms').checked;
    $('#termsRow').classList.toggle('invalid', !termsOk);
    if(!termsOk) valid = false;
    if(!valid){
      var firstBad = document.querySelector('.field.invalid, .terms.invalid');
      if(firstBad) firstBad.scrollIntoView({behavior:'smooth', block:'center'});
      return;
    }
    /* sukses — ganti form dengan panel konfirmasi */
    var code = 'MRK-2026-' + String(Math.floor(1000 + Math.random()*9000));
    $('#regCode').textContent = code;
    form.style.display = 'none';
    document.querySelector('.reg-side').style.display = 'none';
    var box = $('#successBox');
    box.hidden = false;
    box.querySelector('.success').setAttribute('data-rv','');
    requestAnimationFrame(function(){ box.querySelector('.success').classList.add('on'); });
    window.scrollTo({top:0, behavior:'smooth'});
    /* Di sini hubungkan ke backend Anda, cth:
       new FormData(form) + storedFile → fetch('/api/register', {...}) */
  });
})();
</script>

</body>
</html>
