<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maurekap — Rekap Pembayaran untuk Usaha Ekspedisi</title>
<meta name="description" content="Maurekap membantu agen ekspedisi merekap pembayaran transfer & QRIS secara otomatis. Bukti tersimpan rapi, rekap harian cepat, monitoring real-time.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Caveat:wght@600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/x-icon" href="{{ asset('img/favicon_io/favicon.ico') }}">
<style>
/* ================= TOKEN & DASAR ================= */
:root{
  --blue:#2563EB; --blue-600:#1D4ED8; --blue-700:#1E40AF;
  --blue-50:#EFF6FF; --blue-100:#DBEAFE;
  --navy:#0F1B33; --ink:#0F172A; --muted:#5B6B84; --line:#E5EAF3;
  --orange:#F97316; --orange-600:#EA580C;
  --green:#16A34A; --green-50:#DCFCE7;
  --amber:#EA580C; --amber-50:#FFEDD5;
  --red:#EF4444;
  --bg:#F6F8FC; --card:#FFFFFF;
  --shadow:0 10px 30px rgba(15,27,51,.08);
  --shadow-sm:0 4px 14px rgba(15,27,51,.07);
  --font:'Plus Jakarta Sans',system-ui,sans-serif;
  --hand:'Caveat',cursive;
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth;scroll-padding-top:92px}
body{font-family:var(--font);background:var(--card);color:var(--ink);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
img{display:block;max-width:100%}
a{color:inherit;text-decoration:none}
ul{list-style:none}
.container{width:min(1160px,92%);margin-inline:auto}
h1,h2,h3{color:var(--navy);line-height:1.15;letter-spacing:-.01em}
::selection{background:var(--blue);color:#fff}
:focus-visible{outline:2px solid var(--blue);outline-offset:3px;border-radius:4px}

/* reveal on scroll */
[data-rv]{opacity:0;transform:translateY(26px);transition:opacity .7s ease,transform .7s cubic-bezier(.2,.7,.2,1);transition-delay:var(--d,0s)}
[data-rv].on{opacity:1;transform:none}

/* ================= TOMBOL ================= */
.btn{display:inline-flex;align-items:center;gap:9px;font-family:var(--font);font-weight:700;font-size:.95rem;
  padding:14px 26px;border-radius:10px;border:2px solid transparent;cursor:pointer;transition:.22s;white-space:nowrap}
.btn .arr{transition:transform .2s}
.btn:hover .arr{transform:translateX(4px)}
.btn-blue{background:var(--blue);color:#fff;box-shadow:0 8px 20px rgba(37,99,235,.28)}
.btn-blue:hover{background:var(--blue-600);transform:translateY(-2px)}
.btn-outline{background:#fff;color:var(--blue);border-color:var(--blue)}
.btn-outline:hover{background:var(--blue-50)}
.btn-orange{background:var(--orange);color:#fff;box-shadow:0 8px 20px rgba(249,115,22,.3)}
.btn-orange:hover{background:var(--orange-600);transform:translateY(-2px)}
.btn-sm{padding:10px 20px;font-size:.88rem;border-width:1.5px}

/* ================= TAG PEMBUKA SEKSI ================= */
.sec-tag{display:inline-flex;align-items:center;gap:8px;background:var(--blue-50);border:1px solid var(--blue-100);
  color:var(--blue-700);font-size:.68rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;
  padding:7px 15px;border-radius:30px;margin-bottom:16px}
.sec-tag::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--blue);flex:none}
.sec-tag.on-blue{background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.28);color:#fff}
.sec-tag.on-blue::before{background:#FDBA74}

/* ================= HEADER ================= */
header{position:sticky;top:0;z-index:60;background:#fff;border-bottom:1px solid var(--line);transition:box-shadow .3s}
header.scrolled{box-shadow:0 6px 24px rgba(15,27,51,.09)}
.head-in{display:flex;align-items:center;gap:26px;height:70px}
.logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:1.18rem;color:var(--navy);margin-right:auto}
.logo-badge{width:34px;height:34px;border-radius:9px;background:var(--blue);display:grid;place-items:center;color:#fff;flex:none}
.nav{display:flex;align-items:center;gap:26px}
.nav a:not(.btn){font-weight:600;font-size:.92rem;color:#3B4A63;position:relative;padding:6px 0}
.nav a:not(.btn)::after{content:"";position:absolute;left:0;bottom:0;height:2px;width:0;background:var(--blue);border-radius:2px;transition:width .25s}
.nav a:not(.btn):hover::after{width:100%}
.nav a:not(.btn):hover{color:var(--blue)}
.nav-cta{display:flex;gap:10px;align-items:center}
.nav-toggle{display:none}
.burger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:8px;z-index:70}
.burger span{width:23px;height:2.5px;background:var(--navy);border-radius:2px;transition:.3s}

/* ================= HERO ================= */
.hero{background:linear-gradient(180deg,#EAF1FF 0%,#F6F8FC 100%);padding:clamp(52px,7vw,92px) 0 clamp(64px,8vw,110px);position:relative;overflow:hidden}
.hero::before{content:"";position:absolute;inset:0;background-image:radial-gradient(rgba(37,99,235,.08) 1px,transparent 1px);background-size:26px 26px;pointer-events:none}
.hero-grid{display:grid;grid-template-columns:1fr 1.05fr;gap:clamp(36px,4vw,60px);align-items:center;position:relative}
.pill{display:inline-flex;align-items:center;gap:8px;background:var(--blue-100);color:var(--blue-700);
  font-size:.7rem;font-weight:800;letter-spacing:.1em;padding:8px 16px;border-radius:30px;margin-bottom:22px}
.hero h1{font-size:clamp(1.9rem,1.1rem + 3.6vw,3.3rem);font-weight:800;margin-bottom:18px}
.hero h1 .acc{color:var(--blue)}
.hero-sub{color:var(--muted);font-size:clamp(.95rem,.9rem + .3vw,1.05rem);max-width:50ch;margin-bottom:26px}
.hero-sub b{color:var(--ink)}
.checks{display:grid;gap:12px;margin-bottom:30px}
.checks li{display:flex;gap:11px;align-items:center;font-weight:600;font-size:.94rem;color:#28354C}
.ck-ic{width:22px;height:22px;flex:none;border-radius:50%;background:var(--blue);color:#fff;display:grid;place-items:center}
.ck-ic svg{width:12px;height:12px}
.hero-cta{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:18px}
.micro{display:flex;gap:22px;flex-wrap:wrap}
.micro span{display:flex;align-items:center;gap:7px;font-size:.82rem;color:var(--muted);font-weight:500}
.micro svg{width:15px;height:15px;color:var(--muted);flex:none}

/* --- mockup laptop + HP (semua satuan em agar ikut berskala) --- */
.mock{position:relative;padding:0 0 44px 96px}
.laptop{width:100%;max-width:600px}
.lap-screen{border:9px solid #101828;border-bottom-width:12px;border-radius:14px 14px 0 0;overflow:hidden;background:#fff;box-shadow:0 30px 60px -20px rgba(15,27,51,.3)}
.dash{display:grid;grid-template-columns:12em 1fr;min-height:30em;font-size:clamp(6.4px,2px + 0.75vw,10px)}
.dash-side{background:#F8FAFD;border-right:1px solid var(--line);padding:1.2em 1em}
.dash-logo{display:flex;align-items:center;gap:.6em;font-weight:800;font-size:1.05em;color:var(--navy);margin-bottom:1.4em}
.dash-logo i{width:1.7em;height:1.7em;border-radius:.5em;background:var(--blue);color:#fff;display:grid;place-items:center;font-style:normal;font-size:.9em}
.dash-side li{padding:.6em .8em;border-radius:.6em;color:#64748B;font-weight:600;margin-bottom:.25em;display:flex;gap:.6em;align-items:center}
.dash-side li::before{content:"";width:.5em;height:.5em;border-radius:50%;background:#C3CEDC;flex:none}
.dash-side li.active{background:var(--blue);color:#fff}
.dash-side li.active::before{background:#fff}
.dash-main{padding:1.2em 1.4em;min-width:0}
.dash-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:.8em;margin-bottom:1em}
.d-stat{border:1px solid var(--line);border-radius:.8em;padding:.8em 1em;background:#fff;min-width:0}
.d-stat .lb{font-size:.76em;color:#8A97AB;font-weight:700;letter-spacing:.03em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.d-stat .vl{font-size:1.25em;font-weight:800;color:var(--navy);margin:.2em 0;white-space:nowrap}
.d-stat .vl.blue{color:var(--blue)}
.d-stat .vl.red{color:var(--red)}
.d-stat .sb{font-size:.7em;color:#A5B1C2}
.d-chart{border:1px solid var(--line);border-radius:.8em;padding:.8em 1em;margin-bottom:1em}
.d-chart .lb{font-size:.85em;font-weight:800;color:var(--navy);margin-bottom:.4em}
.d-chart svg{width:100%;height:7.4em;display:block}
.chart-line{stroke:var(--blue);stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;
  stroke-dasharray:700;stroke-dashoffset:700;animation:draw 2s .6s ease forwards}
.chart-area{fill:url(#gradB);opacity:0;animation:fadein 1s 1.8s forwards}
.chart-dot{fill:#fff;stroke:var(--blue);stroke-width:1.6;opacity:0;animation:fadein .4s 2s forwards}
@keyframes draw{to{stroke-dashoffset:0}}
@keyframes fadein{to{opacity:1}}
.d-x{display:flex;justify-content:space-between;font-size:.66em;color:#A5B1C2;margin-top:.3em}
.d-table .lb{font-size:.85em;font-weight:800;color:var(--navy);display:flex;justify-content:space-between;margin-bottom:.5em}
.d-table .lb em{font-style:normal;color:var(--blue);font-size:.8em}
.d-table table{width:100%;border-collapse:collapse}
.d-table th{font-size:.7em;color:#8A97AB;text-align:left;font-weight:700;padding:.3em .4em;border-bottom:1px solid var(--line);white-space:nowrap}
.d-table td{font-size:.76em;color:#3B4A63;font-weight:600;padding:.5em .4em;border-bottom:1px solid #F1F5F9;white-space:nowrap}
.pill-st{font-size:.66em;font-weight:800;padding:.25em .8em;border-radius:10px}
.pill-st.ok{background:var(--green-50);color:var(--green)}
.pill-st.wait{background:var(--amber-50);color:var(--amber)}
.lap-base{height:13px;background:linear-gradient(#E8ECF2,#B9C2CF);border-radius:0 0 12px 12px;position:relative}
.lap-base::after{content:"";position:absolute;left:50%;top:0;transform:translateX(-50%);width:72px;height:5px;background:#98A3B3;border-radius:0 0 6px 6px}

/* phone */
.phone{position:absolute;left:0;bottom:0;width:172px;background:#0B1526;border-radius:26px;padding:8px;
  box-shadow:0 30px 60px -18px rgba(15,27,51,.45);animation:floaty 6s ease-in-out infinite;z-index:2}
@keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.phone-screen{background:#fff;border-radius:19px;padding:14px 12px;overflow:hidden}
.ph-head{display:flex;justify-content:space-between;align-items:center;font-size:8.5px;font-weight:800;color:var(--navy);margin-bottom:10px}
.ph-head span{color:#B6C0CF}
.ph-row{margin-bottom:8px}
.ph-row .lb{font-size:6.8px;color:#8A97AB;font-weight:700}
.ph-row .vl{font-size:8.6px;font-weight:700;color:var(--ink)}
.ph-row .vl.big{font-size:12px;font-weight:800}
.ph-status{display:inline-block;font-size:6.8px;font-weight:800;background:var(--green-50);color:var(--green);padding:3px 9px;border-radius:10px;margin-bottom:10px}
.qr-box{border:1px solid var(--line);border-radius:8px;padding:8px;display:grid;place-items:center;margin-bottom:10px}
.qr-box svg{width:74px;height:74px}
.ph-btn{display:block;text-align:center;background:var(--blue);color:#fff;font-size:8px;font-weight:800;border-radius:7px;padding:7px 0}

/* anotasi tulisan tangan */
.annot{position:absolute;left:200px;bottom:-12px;display:flex;align-items:flex-end;gap:6px;color:var(--blue);z-index:1}
.annot p{font-family:var(--hand);font-size:clamp(1.05rem,1rem + .6vw,1.4rem);line-height:1.1;transform:rotate(-3deg);max-width:160px}
.annot svg{width:74px;height:52px;flex:none}
.annot .draw{stroke:var(--blue);stroke-width:2;fill:none;stroke-linecap:round;stroke-dasharray:200;stroke-dashoffset:200;animation:draw 1.4s 1.4s ease forwards}

/* ================= TRUST STRIP ================= */
.trust{padding:0 0 26px;margin-top:-30px;position:relative;z-index:3}
.trust-card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow);padding:clamp(22px,3vw,30px) clamp(18px,3vw,32px);text-align:center}
.trust-card>p{font-size:.88rem;color:var(--muted);margin-bottom:22px}
.trust-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:14px;align-items:center}
.trust-item{display:flex;flex-direction:column;align-items:center;gap:9px;text-align:center;transition:transform .2s}
.trust-item:hover{transform:translateY(-3px)}
.trust-item svg{width:30px;height:30px;color:#DC2626}
.trust-item b{font-size:.82rem;color:#33415C;font-weight:700}
.trust-item.more b{color:var(--muted);font-weight:600}

/* ================= SEKSI UMUM ================= */
.sec{padding:clamp(68px,9vw,108px) 0}
.sec-head{text-align:center;max-width:680px;margin:0 auto clamp(40px,5vw,58px)}
.sec-head h2{font-size:clamp(1.55rem,1.1rem + 2.2vw,2.3rem);font-weight:800;margin-bottom:14px}
.sec-head p{color:var(--muted);font-size:clamp(.92rem,.88rem + .25vw,1rem);margin-inline:auto;max-width:60ch}
.sec-alt{background:var(--bg)}

/* ================= KEUNGGULAN ================= */
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.feat{background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px;box-shadow:var(--shadow-sm);transition:.25s}
.feat:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(15,27,51,.12);border-color:var(--blue-100)}
.feat-ic{width:46px;height:46px;border-radius:12px;background:var(--blue-50);color:var(--blue);display:grid;place-items:center;margin-bottom:18px}
.feat-ic svg{width:22px;height:22px}
.feat h3{font-size:1.05rem;font-weight:800;margin-bottom:8px}
.feat p{font-size:.9rem;color:var(--muted)}

/* ================= MASALAH ================= */
.prob-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:18px}
.prob{background:#fff;border:1px solid var(--line);border-radius:14px;padding:26px 18px;text-align:center;box-shadow:var(--shadow-sm);transition:.25s}
.prob:hover{transform:translateY(-5px);box-shadow:0 16px 34px rgba(15,27,51,.1)}
.prob-ic{position:relative;width:56px;height:56px;margin:0 auto 16px;border-radius:50%;background:var(--blue-50);color:var(--blue);display:grid;place-items:center}
.prob-ic svg{width:26px;height:26px}
.prob-ic .x{position:absolute;top:-2px;right:-2px;width:18px;height:18px;border-radius:50%;background:var(--red);color:#fff;font-size:10px;font-weight:800;display:grid;place-items:center;border:2px solid #fff}
.prob p{font-size:.86rem;font-weight:600;color:#33415C;line-height:1.45}

/* ================= MANFAAT ================= */
.ben-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.ben{background:#fff;border:1px solid var(--line);border-radius:16px;padding:30px 26px;text-align:center;box-shadow:var(--shadow-sm);transition:.25s}
.ben:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(15,27,51,.12)}
.ben-ic{width:46px;height:46px;margin:0 auto 16px;border-radius:50%;background:var(--green-50);color:var(--green);display:grid;place-items:center}
.ben-ic svg{width:22px;height:22px}
.ben h3{font-size:1rem;font-weight:800;margin-bottom:8px}
.ben p{font-size:.87rem;color:var(--muted)}

/* ================= CARA KERJA ================= */
.steps{display:grid;grid-template-columns:1fr 34px 1fr 34px 1fr 34px 1fr;gap:14px;align-items:stretch}
.step{background:#fff;border:1px solid var(--line);border-radius:16px;padding:26px 22px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;transition:.25s}
.step:hover{transform:translateY(-5px);box-shadow:0 16px 36px rgba(15,27,51,.12)}
.step-top{display:flex;gap:11px;align-items:flex-start;margin-bottom:10px}
.step-num{width:26px;height:26px;flex:none;border-radius:50%;background:var(--blue);color:#fff;font-size:.8rem;font-weight:800;display:grid;place-items:center;margin-top:2px}
.step h3{font-size:.98rem;font-weight:800}
.step>p{font-size:.85rem;color:var(--muted);margin-bottom:18px}
.step-ill{margin-top:auto;background:var(--blue-50);border-radius:12px;padding:20px;display:grid;place-items:center;min-height:104px}
.step-ill svg{width:56px;height:56px;color:var(--blue)}
.step-arrow{display:grid;place-items:center}
.step-arrow svg{width:30px;height:16px}
.step-arrow path{stroke:var(--blue);stroke-width:2;fill:none;stroke-dasharray:5 5;animation:march 1s linear infinite}
@keyframes march{to{stroke-dashoffset:-10}}

/* ================= TESTIMONI ================= */
.testi-wrap{background:var(--blue);border-radius:26px;padding:clamp(44px,6vw,64px) clamp(20px,4vw,56px);box-shadow:0 30px 60px -20px rgba(37,99,235,.45)}
.testi-head{text-align:center;margin-bottom:38px}
.testi-head h2{color:#fff;font-size:clamp(1.5rem,1.1rem + 2vw,2.1rem);font-weight:800;margin-bottom:12px}
.testi-head p{color:rgba(255,255,255,.85);font-size:clamp(.9rem,.86rem + .25vw,1rem);max-width:52ch;margin-inline:auto}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.testi{background:#fff;border-radius:16px;padding:28px;display:flex;flex-direction:column;transition:.25s}
.testi:hover{transform:translateY(-6px)}
.stars{display:flex;gap:3px;margin-bottom:16px}
.stars svg{width:16px;height:16px;fill:#F59E0B}
.testi blockquote{font-size:.92rem;color:#33415C;line-height:1.65;flex:1}
.t-person{display:flex;gap:12px;align-items:center;margin-top:22px}
.t-person img{width:42px;height:42px;border-radius:50%;object-fit:cover}
.t-person b{display:block;font-size:.9rem;color:var(--navy)}
.t-person span{font-size:.78rem;color:var(--muted)}

/* ================= CTA ================= */
.cta-card{background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:var(--shadow);
  padding:clamp(28px,4.5vw,48px) clamp(22px,4vw,52px);display:flex;align-items:center;gap:clamp(20px,3vw,40px);flex-wrap:wrap}
.cta-ic{width:64px;height:64px;flex:none;border-radius:50%;background:var(--blue-50);color:var(--blue);display:grid;place-items:center}
.cta-ic svg{width:30px;height:30px}
.cta-txt{flex:1;min-width:260px}
.cta-txt .sec-tag{margin-bottom:12px}
.cta-txt h2{font-size:clamp(1.25rem,1rem + 1.4vw,1.7rem);font-weight:800;color:var(--blue);margin-bottom:6px}
.cta-txt p{color:var(--muted);font-size:.96rem}
.cta-right{text-align:center}
.cta-right small{display:block;margin-top:10px;font-size:.78rem;color:var(--muted)}

/* ================= FOOTER ================= */
footer{background:var(--navy);color:#9FB0C9}
.foot-grid{display:grid;grid-template-columns:1.6fr 1fr 1fr 1.3fr;gap:40px;padding:clamp(52px,7vw,76px) 0 46px}
.foot-brand .logo{color:#fff;margin-bottom:16px}
.foot-brand p{font-size:.88rem;max-width:32ch;margin-bottom:20px}
.socials{display:flex;gap:10px}
.socials a{width:36px;height:36px;border-radius:50%;border:1px solid rgba(159,176,201,.35);display:grid;place-items:center;transition:.2s}
.socials a:hover{background:var(--blue);border-color:var(--blue);color:#fff;transform:translateY(-3px)}
.socials svg{width:16px;height:16px}
footer h4{color:#fff;font-size:.82rem;font-weight:800;letter-spacing:.06em;margin-bottom:18px}
footer ul li{margin-bottom:11px}
footer ul a{font-size:.88rem;transition:.2s}
footer ul a:hover{color:#fff;padding-left:4px}
.contact li{display:flex;gap:11px;align-items:flex-start;font-size:.88rem;margin-bottom:14px}
.contact svg{width:17px;height:17px;flex:none;margin-top:3px;color:var(--blue)}
.foot-bottom{border-top:1px solid rgba(159,176,201,.18);padding:22px 0;text-align:center;font-size:.8rem}

/* ================= RESPONSIVE ================= */
/* layar lebar */
@media (min-width:1440px){
  .container{width:min(1220px,92%)}
  .hero-grid{gap:70px}
}
/* laptop kecil / tablet landscape */
@media (max-width:1024px){
  .hero-grid{grid-template-columns:1fr;gap:70px}
  .mock{margin:0 auto;max-width:620px}
  .feat-grid,.ben-grid{grid-template-columns:repeat(2,1fr)}
  .prob-grid{grid-template-columns:repeat(3,1fr)}
  .steps{grid-template-columns:1fr 1fr;gap:18px}
  .step-arrow{display:none}
  .testi-grid{grid-template-columns:1fr;max-width:540px;margin:0 auto}
  .foot-grid{grid-template-columns:1fr 1fr}
}
/* tablet portrait */
@media (max-width:920px){
  .burger{display:flex}
  .nav{position:fixed;top:0;right:0;height:100dvh;width:min(320px,84%);background:#fff;flex-direction:column;
    align-items:flex-start;justify-content:flex-start;padding:96px 34px;gap:22px;transform:translateX(105%);
    transition:transform .35s cubic-bezier(.2,.7,.2,1);box-shadow:-20px 0 50px rgba(15,27,51,.15);z-index:65;overflow-y:auto}
  .nav a:not(.btn){font-size:1.05rem}
  .nav-cta{flex-direction:column;align-items:stretch;width:100%;margin-top:10px}
  .nav-cta .btn{justify-content:center}
  .nav-toggle:checked ~ .nav{transform:none}
  .nav-toggle:checked ~ .burger span:nth-child(1){transform:translateY(7.5px) rotate(45deg)}
  .nav-toggle:checked ~ .burger span:nth-child(2){opacity:0}
  .nav-toggle:checked ~ .burger span:nth-child(3){transform:translateY(-7.5px) rotate(-45deg)}
}
/* mobile besar */
@media (max-width:768px){
  .mock{padding-left:0;padding-bottom:6px}
  .phone{position:relative;left:0;margin:-46px auto 0}
  .annot{position:static;margin:20px auto 0;justify-content:center;align-items:center}
  .trust-grid{grid-template-columns:repeat(3,1fr);gap:22px}
  .prob-grid{grid-template-columns:repeat(2,1fr)}
}
/* mobile kecil */
@media (max-width:640px){
  .steps{grid-template-columns:1fr}
  .foot-grid{grid-template-columns:1fr;gap:34px}
  .cta-right{width:100%}
  .cta-right .btn{width:100%;justify-content:center}
  .trust{margin-top:-16px}
}
/* mobile sangat kecil */
@media (max-width:420px){
  .head-in{height:62px;gap:14px}
  .logo{font-size:1.05rem}
  .logo-badge{width:30px;height:30px}
  .hero-cta .btn{width:100%;justify-content:center}
  .micro{gap:14px}
  .trust-grid{grid-template-columns:repeat(2,1fr)}
  .prob-grid{grid-template-columns:1fr}
  .feat-grid,.ben-grid{grid-template-columns:1fr}
  .phone{width:158px}
  .annot svg{width:56px;height:40px}
  .cta-ic{width:54px;height:54px}
  .cta-ic svg{width:26px;height:26px}
}
@media (max-width:340px){
  .container{width:94%}
  .pill{font-size:.62rem;padding:7px 12px}
  .trust-grid{grid-template-columns:1fr 1fr;gap:18px}
  .phone{width:148px}
}
/* layar pendek / landscape HP */
@media (max-height:520px) and (orientation:landscape){
  .hero{padding:44px 0 60px}
  .hero-grid{grid-template-columns:1fr;gap:48px}
  html{scroll-padding-top:80px}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation:none!important;transition:none!important}
  html{scroll-behavior:auto}
  [data-rv]{opacity:1;transform:none}
  .chart-line{stroke-dashoffset:0}
  .chart-area,.chart-dot{opacity:1}
  .annot .draw{stroke-dashoffset:0}
}
</style>
</head>
<body>

<!-- ============ HEADER ============ -->
<header id="header">
  <div class="container head-in">
    <a class="logo" href="#beranda">
      <span class="logo-badge" style="background:#fff;">
        <img src="{{ asset('img/logo/maurekap-icon-hd-transparent.png') }}" alt="Logo Maurekap" width="44" height="44" style="display:block;">
      </span>
      MAUREKAP
    </a>
    <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-label="Buka menu">
    <label for="nav-toggle" class="burger" aria-label="Toggle menu"><span></span><span></span><span></span></label>
   <nav class="nav">
  <a href="#beranda">Beranda</a>
  <a href="#masalah">Masalah</a>
  <a href="#manfaat">Solusi</a>
  <a href="#cara-kerja">Cara Kerja</a>
  <a href="#harga">Harga</a>
  {{-- <a href="#testimoni">Testimoni</a> --}}
  <div class="nav-cta">
    <a href="#" class="btn btn-outline btn-sm">Login</a>
    <a href="{{ route('daftar') }}" class="btn btn-blue btn-sm">Coba Gratis</a>
  </div>
</nav>
  </div>
</header>

<main id="beranda">

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="pill" data-rv>SISTEM REKAP PEMBAYARAN UNTUK AGEN EKSPEDISI</span>
      <h1 data-rv style="--d:.08s">Punya Agen Multi Ekspedisi?<br>Rekap Pembayaran<br><span class="acc">Jadi Mudah &amp; Rapi</span></h1>
      <p class="hero-sub" data-rv style="--d:.16s"><b>Maurekap</b> adalah sistem rekap pembayaran untuk pelaku usaha agen ekspedisi. Semua bukti pembayaran tersimpan rapi, mudah dicek, dan bisa dimonitor kapan saja.</p>
      <ul class="checks" data-rv style="--d:.24s">
        <li>
          <span class="ck-ic">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </span>
          Semua ekspedisi dalam satu dashboard
        </li>
        <li>
          <span class="ck-ic">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </span>
          Upload &amp; cek bukti QRIS / Transfer
        </li>
        <li>
          <span class="ck-ic">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </span>
          Verifikasi transaksi oleh Finance
        </li>
        <li>
          <span class="ck-ic">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </span>
          Pantau laporan per outlet, admin &amp; ekspedisi
        </li>
      </ul>

      <div class="hero-cta" data-rv style="--d:.32s">
        <a href="#cta" class="btn btn-blue">Coba Gratis 7 Hari <span class="arr">→</span></a>
        <a href="#cara-kerja" class="btn btn-outline">Lihat Demo</a>
      </div>
      <div class="micro" data-rv style="--d:.4s">
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>Tanpa kartu kredit</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>Setup cepat</span>
      </div>
    </div>

    <!-- Mockup laptop + HP -->
    <div class="mock" data-rv style="--d:.2s">
      <div class="laptop">
        <div class="lap-screen">
          <div class="dash">
            <aside class="dash-side">
              <div class="dash-logo"><i>M</i>Maurekap</div>
              <ul>
                <li class="active">Dashboard</li>
                <li>Transaksi</li>
                <li>Seller</li>
                <li>Rekap Harian</li>
                <li>Laporan</li>
                <li>Pengaturan</li>
              </ul>
            </aside>
            <div class="dash-main">
              <div class="dash-stats">
                <div class="d-stat"><p class="lb">Total Transaksi</p><p class="vl"><span class="count" data-count="1250">1.250</span></p><p class="sb">Hari ini</p></div>
                <div class="d-stat"><p class="lb">Total Masuk</p><p class="vl blue">Rp <span class="count" data-count="46678000">46.678.000</span></p><p class="sb">Hari ini</p></div>
                <div class="d-stat"><p class="lb">Belum Verifikasi</p><p class="vl red"><span class="count" data-count="32">32</span></p><p class="sb">Transaksi</p></div>
              </div>
              <div class="d-chart">
                <p class="lb">Grafik Transaksi</p>
                <svg viewBox="0 0 320 90" preserveAspectRatio="none" aria-hidden="true">
                  <defs><linearGradient id="gradB" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2563EB" stop-opacity=".22"/><stop offset="1" stop-color="#2563EB" stop-opacity="0"/></linearGradient></defs>
                  <g stroke="#EEF2F8" stroke-width="1"><line x1="0" y1="20" x2="320" y2="20"/><line x1="0" y1="45" x2="320" y2="45"/><line x1="0" y1="70" x2="320" y2="70"/></g>
                  <path class="chart-area" d="M8,62 L52,50 L96,56 L140,34 L184,42 L228,20 L272,34 L312,26 L312,90 L8,90 Z"/>
                  <path class="chart-line" d="M8,62 L52,50 L96,56 L140,34 L184,42 L228,20 L272,34 L312,26"/>
                  <g><circle class="chart-dot" cx="52" cy="50" r="3"/><circle class="chart-dot" cx="140" cy="34" r="3"/><circle class="chart-dot" cx="228" cy="20" r="3"/><circle class="chart-dot" cx="312" cy="26" r="3"/></g>
                </svg>
                <div class="d-x"><span>01/05</span><span>03/05</span><span>05/05</span><span>07/05</span></div>
              </div>
              <div class="d-table">
                <p class="lb">Transaksi Terbaru <em>Lihat →</em></p>
                <table>
                  <thead>
                    <tr>
                      <th>Waktu</th>
                      <th>Seller</th>
                      <th>Ekspedisi</th>
                      <th>Metode</th>
                      <th>Nominal</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>07 Mei 10:48</td>
                      <td>Rina Konveksi</td>
                      <td>
                        <span style="font-weight:bold; color:#bf0000;">JNE</span>
                      </td>
                      <td>QRIS</td>
                      <td>Rp 85.000</td>
                      <td><span class="pill-st ok">Terverifikasi</span></td>
                    </tr>
                    <tr>
                      <td>07 Mei 10:32</td>
                      <td>Rumah Kado</td>
                      <td>
                        <span style="font-weight:bold; color:#e20e19;">J&amp;T</span>
                      </td>
                      <td>Transfer</td>
                      <td>Rp 120.000</td>
                      <td><span class="pill-st wait">Menunggu</span></td>
                    </tr>
                    <tr>
                      <td>07 Mei 10:21</td>
                      <td>Dapur Mami</td>
                      <td>
                        <span style="font-weight:bold; color:#f36f21;">Lion Parcel</span>
                      </td>
                      <td>QRIS</td>
                      <td>Rp 63.000</td>
                      <td><span class="pill-st ok">Terverifikasi</span></td>
                    </tr>
                  </tbody>
                </table>

              </div>
            </div>
          </div>
        </div>
        <div class="lap-base"></div>
      </div>

      <div class="phone" aria-hidden="true">
        <div class="phone-screen">
          <div class="ph-head">Bukti Transfer <span>✕</span></div>
          <div class="ph-row"><p class="lb">Dari</p><p class="vl">Rizky Rudy</p></div>
          <div class="ph-row"><p class="lb">Nominal</p><p class="vl big">QRIS · Rp 85.000</p></div>
          <div class="ph-row"><p class="lb">Waktu</p><p class="vl">12 Mei 2026 · 14:36</p></div>
          <span class="ph-status">✓ Terverifikasi</span>
          <div class="qr-box">
            <svg viewBox="0 0 21 21" shape-rendering="crispEdges" fill="#0F172A" aria-hidden="true">
              <path d="M0 0h7v7H0z"/><rect x="2" y="2" width="3" height="3" fill="#fff"/><rect x="3" y="3" width="1" height="1"/>
              <path d="M14 0h7v7h-7z"/><rect x="16" y="2" width="3" height="3" fill="#fff"/><rect x="17" y="3" width="1" height="1"/>
              <path d="M0 14h7v7H0z"/><rect x="2" y="16" width="3" height="3" fill="#fff"/><rect x="3" y="17" width="1" height="1"/>
              <rect x="9" y="0" width="1" height="1"/><rect x="11" y="1" width="1" height="2"/><rect x="9" y="3" width="2" height="1"/><rect x="12" y="4" width="1" height="1"/>
              <rect x="0" y="9" width="2" height="1"/><rect x="3" y="10" width="1" height="1"/><rect x="5" y="9" width="1" height="2"/><rect x="8" y="9" width="1" height="1"/>
              <rect x="10" y="10" width="2" height="1"/><rect x="13" y="9" width="1" height="2"/><rect x="15" y="10" width="1" height="1"/><rect x="18" y="9" width="2" height="1"/>
              <rect x="20" y="11" width="1" height="2"/><rect x="9" y="12" width="1" height="2"/><rect x="11" y="13" width="2" height="1"/><rect x="14" y="12" width="1" height="1"/>
              <rect x="16" y="13" width="2" height="1"/><rect x="19" y="14" width="1" height="1"/><rect x="9" y="16" width="2" height="1"/><rect x="12" y="15" width="1" height="2"/>
              <rect x="14" y="17" width="1" height="1"/><rect x="16" y="16" width="1" height="2"/><rect x="18" y="18" width="2" height="1"/><rect x="9" y="19" width="1" height="1"/>
              <rect x="11" y="18" width="1" height="2"/><rect x="14" y="19" width="2" height="1"/><rect x="20" y="17" width="1" height="2"/>
            </svg>
          </div>
          <span class="ph-btn">Lihat Detail</span>
        </div>
      </div>

      <div class="annot" aria-hidden="true">
        <p>Semua transaksi dalam satu dashboard</p>
        <svg viewBox="0 0 74 52"><path class="draw" d="M6 46 C 26 44, 48 34, 62 12 M62 12 l-9 3 M62 12 l1 10"/></svg>
      </div>
    </div>
  </div>
</section>

<!-- ============ TRUST STRIP ============ -->
{{-- <div class="trust">
  <div class="container">
    <div class="trust-card" data-rv>
      <span class="sec-tag">Dipercaya Luas</span>
      <p>Dipercaya oleh ratusan pelaku usaha agen ekspedisi di seluruh Indonesia</p>
      <div class="trust-grid">
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1.5-5h15L21 9"/><path d="M3 9h18v11H3z"/><path d="M9 20v-6h6"/><path d="M3 9c0 1.5 1.3 2.5 3 2.5S9 10.5 9 9c0 1.5 1.3 2.5 3 2.5s3-1 3-2.5c0 1.5 1.3 2.5 3 2.5s3-1 3-2.5"/></svg><b>Agen JNE</b></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5"/><path d="M12 13v8"/></svg><b>Agen J&amp;T</b></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h6l-1 8 9-12h-6z"/></svg><b>Agen SiCepat</b></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.6"/></svg><b>Agen Ninja Xpress</b></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7V6a6 6 0 0 1 12 0v1"/><path d="M4 7h16l-1.5 13h-13z"/></svg><b>Agen SPX</b></div>
        <div class="trust-item more"><b>dan banyak<br>lainnya</b></div>
      </div>
    </div>
  </div>
</div> --}}

<!-- ============ KEUNGGULAN ============ -->
{{-- <section class="sec" id="keunggulan">
  <div class="container">
    <div class="sec-head" data-rv>
      <span class="sec-tag">Keunggulan Sistem</span>
      <h2>Semua yang Usaha Ekspedisi Anda Butuhkan, dalam Satu Sistem</h2>
      <p>Dirancang khusus mengikuti alur kerja agen ekspedisi harian — bukan aplikasi pembukuan umum yang dipaksakan. Ini alasan kenapa ratusan agen memilih Maurekap.</p>
    </div>
    <div class="feat-grid">
      <article class="feat" data-rv>
        <div class="feat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h6l-1 8 9-12h-6z"/></svg></div>
        <h3>Rekap Otomatis &amp; Real-Time</h3>
        <p>Setiap transaksi langsung tercatat lengkap dengan nominal, seller, metode bayar, dan waktunya.</p>
      </article>
      <article class="feat" data-rv style="--d:.08s">
        <div class="feat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6z"/><path d="M9 12l2 2 4-4.5"/></svg></div>
        <h3>Bukti Pembayaran Tersimpan Aman</h3>
        <p>Bukti transfer &amp; QRIS tersimpan rapi di cloud — tidak lagi tercecer di chat WhatsApp.</p>
      </article>
      <article class="feat" data-rv style="--d:.16s">
        <div class="feat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.7-3.5 3.3-5.5 6.5-5.5s5.8 2 6.5 5.5"/><circle cx="17.5" cy="9" r="2.6"/><path d="M16 14.7c2.8.2 4.9 2 5.5 4.8"/></svg></div>
        <h3>Multi-Admin &amp; Multi-Cabang</h3>
        <p>Tim bekerja bersamaan tanpa takut data tertimpa. Semua tetap sinkron secara real-time.</p>
      </article>
      <article class="feat" data-rv>
        <div class="feat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 9a6 6 0 1 0-12 0c0 5-2 6-2 6h16s-2-1-2-6"/><path d="M10 19a2 2 0 0 0 4 0"/></svg></div>
        <h3>Notifikasi Pembayaran Masuk</h3>
        <p>Langsung tahu saat uang masuk, sehingga tidak perlu menebak-nebak atau cek mutasi manual.</p>
      </article>
      <article class="feat" data-rv style="--d:.08s">
        <div class="feat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/><path d="M8 15h3M8 18h5"/><path d="M14.5 17.5l1.5 1.5 3-3.5"/></svg></div>
        <h3>Laporan Harian Siap Pakai</h3>
        <p>Ekspor rekap harian, mingguan, dan bulanan sekali klik — siap disetor ke pusat atau principal.</p>
      </article>
      <article class="feat" data-rv style="--d:.16s">
        <div class="feat-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><circle cx="12" cy="15" r="1.6"/></svg></div>
        <h3>Data Aman &amp; Terenkripsi</h3>
        <p>Backup otomatis setiap hari. Data usaha Anda tetap milik Anda, aman dan terkontrol.</p>
      </article>
    </div>
  </div>
</section> --}}

<!-- ============ MASALAH ============ -->
<section class="sec sec-alt" id="masalah">
  <div class="container">
    <div class="sec-head" data-rv>
      <span class="sec-tag">Masalah di Lapangan</span>
      <h2>Masalah yang Sering Dihadapi Agen Ekspedisi</h2>
      <p>Kelihatannya sepele, tapi terjadi hampir setiap hari. Jika dibiarkan, celah-celah kecil inilah yang diam-diam menggerus waktu, tenaga, dan keuntungan usaha Anda.</p>
    </div>
    <div class="prob-grid">
      <article class="prob" data-rv>
        <div class="prob-ic" style="background:#E8F8EE;color:#16A34A"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3z"/><path d="M8.8 9.2c.4 2.6 3 5.2 5.6 5.6l1.4-1.4-2.2-1.2-1 .7c-.8-.4-1.7-1.3-2.1-2.1l.7-1z"/></svg><span class="x">✕</span></div>
        <p>Bukti pembayaran tercecer di WhatsApp</p>
      </article>
      <article class="prob" data-rv style="--d:.08s">
        <div class="prob-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2.5"/></svg><span class="x">✕</span></div>
        <p>Susah memastikan uang sudah benar-benar masuk</p>
      </article>
      <article class="prob" data-rv style="--d:.16s">
        <div class="prob-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/><path d="M8 13h8M8 17h6"/></svg><span class="x">✕</span></div>
        <p>Rekap manual memakan waktu berjam-jam</p>
      </article>
      <article class="prob" data-rv style="--d:.24s">
        <div class="prob-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.7-3.5 3.3-5.5 6.5-5.5s5.8 2 6.5 5.5"/><circle cx="17.5" cy="9" r="2.6"/><path d="M16 14.7c2.8.2 4.9 2 5.5 4.8"/></svg><span class="x">✕</span></div>
        <p>Banyak admin, data tidak sinkron</p>
      </article>
      <article class="prob" data-rv style="--d:.32s">
        <div class="prob-ic" style="background:#FFF6E6;color:#D97706"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3L1.8 20.2h20.4z"/><path d="M12 10v4.5"/><circle cx="12" cy="17.6" r=".4"/></svg><span class="x">✕</span></div>
        <p>Salah input nominal atau nama seller</p>
      </article>
    </div>
  </div>
</section>

<!-- ============ MANFAAT ============ -->
<section class="sec" id="manfaat">
  <div class="container">
    <div class="sec-head" data-rv>
      <span class="sec-tag">Solusi Lengkap</span>
      <h2>Dengan Maurekap, Semua Jadi Lebih Mudah</h2>
      <p>Satu sistem yang membereskan semua masalah di atas — supaya Anda bisa fokus pada hal yang benar-benar penting: mengembangkan usaha.</p>
    </div>
    <div class="ben-grid">
      <article class="ben" data-rv>
        <div class="ben-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
        <h3>Semua pembayaran masuk ke satu tempat</h3>
        <p>Transfer, QRIS, dan tunai tercatat di satu dashboard.</p>
      </article>
      <article class="ben" data-rv style="--d:.06s">
        <div class="ben-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
        <h3>Bukti pembayaran tersimpan otomatis</h3>
        <p>Upload sekali, tersimpan selamanya dan mudah dicari.</p>
      </article>
      <article class="ben" data-rv style="--d:.12s">
        <div class="ben-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></div>
        <h3>Riwayat transaksi mudah dicari</h3>
        <p>Filter berdasarkan seller, tanggal, atau metode bayar.</p>
      </article>
      <article class="ben" data-rv>
        <div class="ben-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20v-1a4 4 0 0 1 4-4h2"/><path d="M16 3.13V7h3.87"/><path d="M16 3l5 5"/><rect x="3" y="13" width="7" height="7" rx="1.5"/><path d="M16 7a5 5 0 1 1-4.27 7.5"/></svg></div>
        <h3>Laporan Lengkap &amp; Detail</h3>
        <p>Laporan harian, bulanan, detail setiap ekspedisi, admin, dan outlet.</p>
      </article>
      <article class="ben" data-rv style="--d:.06s">
        <div class="ben-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><circle cx="7" cy="12" r="2"/><circle cx="17" cy="12" r="2"/><path d="M7 14v2M17 14v2"/><path d="M12 7v-2a4 4 0 0 1 8 0v2"/></svg></div>
        <h3>Multi-Admin &amp; Multi-Outlet</h3>
        <p>Kelola banyak admin, outlet, dan cabang dalam satu sistem.</p>
      </article>
      <article class="ben" data-rv style="--d:.12s">
        <div class="ben-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V7l-8-5-8 5v5c0 6 8 10 8 10z"/><path d="M9 9h6"/><path d="M9 13h6"/></svg></div>
        <h3>Data Aman &amp; Terproteksi</h3>
        <p>Backup otomatis setiap hari. Data bisnis Anda aman dan terkendali.</p>
      </article>

    </div>
  </div>
</section>
<section class="sec sec-alt" id="fitur-utama">
  <div class="container">
    <div class="sec-head" data-rv>
      <span class="sec-tag">Fitur Utama</span>
      <h2>Fitur Utama yang Mendukung Operasional Harian Anda</h2>
    </div>

    <div class="feature-main-grid" style="display: flex; gap: 28px; justify-content: center; align-items: stretch; margin-top: 28px; flex-wrap: wrap;">

      <!-- Kartu 1: Absensi -->
      <div class="main-feature-card" style="background: #fff; border-radius: 15px; padding: 28px 20px 16px 20px; box-shadow: 0 2px 12px rgba(30,80,200,0.04); flex: 1; min-width: 240px; max-width310px; display: flex; flex-direction: column; align-items: flex-start;">
        <div style="background: #EFF6FF; border-radius: 50%; height: 45px; width: 45px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"></circle><path d="M5.5 21a7.5 7.5 0 0 1 13 0"></path></svg>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: .3em; color: #0F172A;">Absensi Karyawan</div>
          <div style="font-size: .95rem; color: #475569; line-height: 1.5;" class="feature-desc">
            Pantau kehadiran admin, finance, kurir, dan tim lapangan secara real-time. Riwayat lengkap dan mudah diakses kapan saja.
          </div>
        </div>
      </div>

      <!-- Kartu 2: Reimburse -->
      <div class="main-feature-card" style="background: #fff; border-radius: 15px; padding: 28px 20px 16px 20px; box-shadow: 0 2px 12px rgba(30,80,200,0.04); flex: 1; min-width: 240px; max-width: 310px; display: flex; flex-direction: column; align-items: flex-start;">
        <div style="background: #EFF6FF; border-radius: 50%; height: 45px; width: 45px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="13" height="13" rx="2"></rect><path d="M16 18h6m-3-3v6"></path><path d="M8.5 13.5L9.5 16l2-2.5"></path></svg>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: .3em; color: #0F172A;">Reimburse & Rembes</div>
          <div style="font-size: .95rem; color: #475569; line-height: 1.5;" class="feature-desc">
            Ajukan dan kelola kebutuhan operasional (bensin, tol, makan) dengan alur persetujuan yang jelas, transparan, dan tercatat rapi.
          </div>
        </div>
      </div>

      <!-- Kartu 3: Laporan (Dilengkapi) -->
      <div class="main-feature-card" style="background: #fff; border-radius: 15px; padding: 28px 20px 16px 20px; box-shadow: 0 2px 12px rgba(30,80,200,0.04); flex: 1; min-width: 240px; max-width: 310px; display: flex; flex-direction: column; align-items: flex-start;">
        <div style="background: #EFF6FF; border-radius: 50%; height: 45px; width: 45px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: .3em; color: #0F172A;">Laporan & Rekap Otomatis</div>
          <div style="font-size: .95rem; color: #475569; line-height: 1.5;" class="feature-desc">
            Dapatkan rekapitulasi keuangan harian, mingguan, dan bulanan secara instan. Ekspor data ke Excel/PDF untuk laporan ke principal.
          </div>
        </div>
      </div>

      <!-- Kartu 4: Multi-Cabang (Tambahan agar layout seimbang) -->
      <div class="main-feature-card" style="background: #fff; border-radius: 15px; padding: 28px 20px 16px 20px; box-shadow: 0 2px 12px rgba(30,80,200,0.04); flex: 1; min-width: 240px; max-width: 310px; display: flex; flex-direction: column; align-items: flex-start;">
        <div style="background: #EFF6FF; border-radius: 50%; height: 45px; width: 45px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"/><circle cx="5" cy="19" r="3"/><circle cx="19" cy="19" r="3"/><path d="M12 8v4"/><path d="M12 12L5 16"/><path d="M12 12l7 4"/></svg>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 1.05rem; margin-bottom: .3em; color: #0F172A;">Manajemen Multi-Cabang</div>
          <div style="font-size: .95rem; color: #475569; line-height: 1.5;" class="feature-desc">
            Kelola operasional dari berbagai cabang atau outlet dalam satu dashboard terpusat. Kontrol penuh tetap di tangan kantor pusat.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<!-- ============ CARA KERJA ============ -->
<section class="sec sec-features" id="cara-kerja">
  <div class="container">
    <div class="sec-head" data-rv>
      <span class="sec-tag">Cara Kerja</span>
      <h2>Cara Kerja Maurekap</h2>
      <p>Tidak perlu paham teknologi. Jika tim Anda bisa memakai WhatsApp, mereka bisa memakai Maurekap — empat langkah sederhana, sisanya berjalan otomatis.</p>
    </div>
    <div class="steps">
      <article class="step" data-rv>
        <div class="step-top"><span class="step-num">1</span><h3>Pembayaran dilakukan</h3></div>
        <p>Customer membayar via Transfer atau QRIS seperti biasa.</p>
        <div class="step-ill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M9.5 5h5"/><rect x="9" y="8" width="6" height="6"/><path d="M9 8h2v2H9zM13 8h2v2h-2zM9 12h2v2H9zM13 12h2v2h-2z" fill="currentColor" stroke="none"/><path d="M10.5 18.5h3"/></svg></div>
      </article>
      <div class="step-arrow" aria-hidden="true"><svg viewBox="0 0 30 16"><path d="M0 8h24m0 0l-5-5m5 5l-5 5"/></svg></div>
      <article class="step" data-rv style="--d:.1s">
        <div class="step-top"><span class="step-num">2</span><h3>Upload bukti pembayaran</h3></div>
        <p>Admin upload bukti pembayaran di Maurekap.</p>
        <div class="step-ill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a5 5 0 0 1-.8-9.9 6 6 0 0 1 11.6 0A5 5 0 0 1 17 18"/><path d="M12 21v-8"/><path d="M8.5 16l3.5-3.5L15.5 16"/></svg></div>
      </article>
      <div class="step-arrow" aria-hidden="true"><svg viewBox="0 0 30 16"><path d="M0 8h24m0 0l-5-5m5 5l-5 5"/></svg></div>
      <article class="step" data-rv style="--d:.2s">
        <div class="step-top"><span class="step-num">3</span><h3>Data otomatis tersimpan</h3></div>
        <p>Maurekap menyimpan nominal, seller, bukti, dan waktu transaksi.</p>
        <div class="step-ill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="11" cy="5.5" rx="8" ry="3"/><path d="M3 5.5v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/><path d="M3 11.5v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/><circle cx="19.5" cy="17.5" r="3.4" fill="#fff"/><path d="M18 17.5l1.2 1.2 2-2.4"/></svg></div>
      </article>
      <div class="step-arrow" aria-hidden="true"><svg viewBox="0 0 30 16"><path d="M0 8h24m0 0l-5-5m5 5l-5 5"/></svg></div>
      <article class="step" data-rv style="--d:.3s">
        <div class="step-top"><span class="step-num">4</span><h3>Owner monitoring</h3></div>
        <p>Semua transaksi bisa dipantau dari dashboard.</p>
        <div class="step-ill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4" width="19" height="13" rx="2"/><path d="M9 21h6M12 17v4"/><path d="M6.5 13l3-3.5 2.5 2 4-5 1.5 2"/></svg></div>
      </article>
    </div>
  </div>
</section>
<!-- ============ HARGA ============ -->
<section class="sec sec-alt" id="harga">
  <div class="container">
    <div class="sec-head" data-rv>
      <span class="sec-tag">Pilihan Paket</span>
      <h2>Pilih Paket yang Cocok untuk Agen Anda</h2>
      <p>Mulai lebih rapi dengan promo spesial untuk periode terbatas. Coba gratis 7 hari dan rasakan sendiri kemudahannya.</p>
      <p style="margin-top:8px;font-size:.88rem;color:var(--blue);font-weight:600">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Promo tersedia untuk periode terbatas
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px;max-width:900px;margin:0 auto" data-rv>
      @foreach($Paket as $item)
        <div style="background:#fff;@if($item->DurasiBulan == 12)border:2px solid var(--blue);box-shadow:0 20px 50px rgba(37,99,235,.15);@else border:2px solid var(--line);box-shadow:var(--shadow-sm);@endif;border-radius:20px;padding:32px 28px;position:relative;transition:.25s">
          <div style="position:absolute;top:-12px;left:28px;@if($item->DurasiBulan == 12)background:var(--blue);color:#fff;@else background:var(--blue-50);border:1px solid var(--blue-100);color:var(--blue-700);@endif;font-size:.7rem;font-weight:800;padding:6px 14px;border-radius:20px;display:flex;align-items:center;gap:6px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ $item->DurasiBulan == 12 ? 'Tahunan' : 'Bulanan' }}
          </div>

          @if($item->DurasiBulan == 1)
          <div style="position:absolute;top:-12px;right:28px;background:linear-gradient(135deg,#FFE4E6,#FEE2E2);color:#DC2626;font-size:.75rem;font-weight:800;padding:6px 14px;border-radius:20px;display:flex;align-items:center;gap:6px;box-shadow:0 4px 12px rgba(220,38,38,.15)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            PROMO SPESIAL 50%
          </div>
          @elseif($item->DurasiBulan == 12)
          <div style="position:absolute;top:-12px;right:28px;background:linear-gradient(135deg,#DCFCE7,#BBF7D0);color:var(--green);font-size:.75rem;font-weight:800;padding:6px 14px;border-radius:20px;display:flex;align-items:center;gap:6px;box-shadow:0 4px 12px rgba(22,163,74,.15)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            PALING HEMAT
          </div>
          @endif

          <div style="margin-top:24px">
            @if($item->DurasiBulan == 1)
              <p style="text-decoration:line-through;color:var(--muted);font-size:.9rem;margin-bottom:4px">Rp249.000/bulan</p>
            @elseif($item->DurasiBulan == 12)
              <p style="text-decoration:line-through;color:var(--muted);font-size:.9rem;margin-bottom:4px">Rp2.490.000/tahun</p>
            @endif

            <div style="display:flex;align-items:flex-start;gap:4px;margin-bottom:12px">
              @if($item->DurasiBulan == 1)
                <span style="font-size:2.8rem;font-weight:800;color:var(--blue);line-height:1">
                  Rp{{ number_format($item->Harga, 0, ',', '.') }}
                </span>
                <span style="color:var(--muted);font-weight:600;margin-top:8px">/bulan</span>
              @elseif($item->DurasiBulan == 12)
                <span style="font-size:2.8rem;font-weight:800;color:var(--blue);line-height:1">
                  Rp{{ number_format($item->Harga, 0, ',', '.') }}
                </span>
                <span style="color:var(--muted);font-weight:600;margin-top:8px">/tahun</span>
              @endif
            </div>
            @if($item->DurasiBulan == 1)
              <p style="background:var(--blue-50);color:var(--blue-700);font-size:.8rem;font-weight:600;padding:8px 12px;border-radius:8px;display:inline-block;margin-bottom:16px">Harga promo untuk 3 bulan pertama</p>
              <p style="font-size:.85rem;color:var(--muted)">Setelah promo: <b style="color:var(--ink)">Rp249.000/bulan</b></p>
            @elseif($item->DurasiBulan == 12)
              <p style="background:var(--green-50);color:var(--green);font-size:.85rem;font-weight:700;padding:8px 12px;border-radius:8px;display:inline-block;margin-bottom:12px">Hemat Rp1.240.000 per tahun</p>
              <p style="font-size:.9rem;color:var(--muted)">Setara <b style="color:var(--ink);font-size:1.05rem">Rp104.000/bulan</b></p>
            @endif
          </div>

          <div style="border-top:1px solid var(--line);margin:24px 0;padding-top:24px">
            <ul style="display:grid;gap:12px">
              @php
                $fiturList = json_decode($item->Fitur, true) ?? [];
              @endphp
              @foreach($fiturList as $fitur)
                <li style="display:flex;gap:10px;align-items:flex-start;font-size:.9rem;color:#33415C;font-weight:600">
                  <span style="width:20px;height:20px;background:var(--blue);border-radius:50%;display:grid;place-items:center;flex:none;margin-top:2px">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </span>
                  {{ $fitur }}
                </li>
              @endforeach
            </ul>
          </div>

          @if($item->DurasiBulan == 1)
            <a href="#" class="btn btn-blue" style="width:100%;justify-content:center;margin-top:8px">Coba Gratis 7 Hari</a>
          @elseif($item->DurasiBulan == 12)
            <a href="#" class="btn btn-blue" style="width:100%;justify-content:center;margin-top:8px;box-shadow:0 8px 24px rgba(37,99,235,.35)">Pilih Paket Tahunan</a>
          @endif
        </div>
      @endforeach
    </div>

    <div style="text-align:center;margin-top:28px" data-rv style="--d:.2s">
      <p style="display:inline-flex;align-items:center;gap:8px;background:var(--blue-50);color:var(--blue-700);padding:10px 18px;border-radius:30px;font-size:.85rem;font-weight:600">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        Aman, transparan, dan dirancang untuk mendukung operasional agen ekspedisi.
      </p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:800px;margin:32px auto 0;text-align:center" data-rv style="--d:.28s">
      <div style="padding:20px">
        <div style="width:48px;height:48px;background:var(--blue-50);border-radius:12px;display:grid;place-items:center;margin:0 auto 12px;color:var(--blue)">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <h4 style="font-size:.95rem;font-weight:800;margin-bottom:6px;color:var(--navy)">Gratis coba 7 hari</h4>
        <p style="font-size:.85rem;color:var(--muted);line-height:1.5">Coba semua fitur Maurekap selama 7 hari penuh.</p>
      </div>
      <div style="padding:20px">
        <div style="width:48px;height:48px;background:var(--blue-50);border-radius:12px;display:grid;place-items:center;margin:0 auto 12px;color:var(--blue)">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <h4 style="font-size:.95rem;font-weight:800;margin-bottom:6px;color:var(--navy)">Semua fitur aktif</h4>
        <p style="font-size:.85rem;color:var(--muted);line-height:1.5">Nikmati semua fitur terbaik tanpa pembatasan.</p>
      </div>
      <div style="padding:20px">
        <div style="width:48px;height:48px;background:var(--blue-50);border-radius:12px;display:grid;place-items:center;margin:0 auto 12px;color:var(--blue)">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
        <h4 style="font-size:.95rem;font-weight:800;margin-bottom:6px;color:var(--navy)">Bisa dibatalkan kapan saja</h4>
        <p style="font-size:.85rem;color:var(--muted);line-height:1.5">Batalkan kapan saja tanpa biaya atau komitmen tambahan.</p>
      </div>
    </div>
  </div>
</section>
<!-- ============ TESTIMONI ============ -->
{{-- <section class="sec" id="testimoni">
  <div class="container">
    <div class="testi-wrap" data-rv>
      <div class="testi-head">
        <span class="sec-tag on-blue">Testimoni</span>
        <h2>Apa Kata Mereka?</h2>
        <p>Cerita nyata dari pelaku usaha ekspedisi yang rekapannya sudah rapi lebih dulu bersama Maurekap.</p>
      </div>
      <div class="testi-grid">
        <article class="testi">
          <div class="stars" aria-label="Rating 5 dari 5">
            <svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg>
          </div>
          <blockquote>"Rekap pembayaran jadi jauh lebih mudah. Tidak perlu lagi cari-cari bukti di grup WhatsApp — semua tersimpan rapi di satu tempat."</blockquote>
          <div class="t-person">
            <img src="https://picsum.photos/seed/dian-permata-jne/84/84" alt="Foto Dian Permata" loading="lazy">
            <div><b>Dian Permata</b><span>Agen JNE</span></div>
          </div>
        </article>
        <article class="testi">
          <div class="stars" aria-label="Rating 5 dari 5">
            <svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg>
          </div>
          <blockquote>"Owner bisa langsung cek pembayaran kapan saja. Admin kerja lebih ringan, usaha makin lancar!"</blockquote>
          <div class="t-person">
            <img src="https://picsum.photos/seed/rudi-hartono-jnt/84/84" alt="Foto Rudi Hartono" loading="lazy">
            <div><b>Rudi Hartono</b><span>Agen J&amp;T</span></div>
          </div>
        </article>
        <article class="testi">
          <div class="stars" aria-label="Rating 5 dari 5">
            <svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg><svg viewBox="0 0 24 24"><path d="M12 2l3 6.6 7 .8-5.2 4.8 1.4 7-6.2-3.6L5.8 21l1.4-7L2 9.4l7-.8z"/></svg>
          </div>
          <blockquote>"Maurekap membantu kami menghemat waktu dan meminimalisir kesalahan input. Sangat direkomendasikan!"</blockquote>
          <div class="t-person">
            <img src="https://picsum.photos/seed/andi-setiawan-sicepat/84/84" alt="Foto Andi Setiawan" loading="lazy">
            <div><b>Andi Setiawan</b><span>Agen SiCepat</span></div>
          </div>
        </article>
      </div>
    </div>
  </div>
</section> --}}

<!-- ============ CTA ============ -->
<section class="sec sec-feature" id="cta">
  <div class="container">
    <div class="cta-card" data-rv>
      <div class="cta-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2"/><path d="M12 15l-3-3a22 22 0 0 1 2-3.9A12.8 12.8 0 0 1 21.5 2.5 12.8 12.8 0 0 1 15.9 13a22 22 0 0 1-3.9 2z"/><path d="M9 12H4s.5-3 2-4c1.6-1 5 0 5 0"/><path d="M12 15v5s3-.5 4-2c1-1.6 0-5 0-5"/><circle cx="15.5" cy="8.5" r="1.8"/></svg></div>
      <div class="cta-txt">
        <span class="sec-tag">Mulai Sekarang</span>
        <h2>Kelola Pembayaran Jadi Lebih Mudah</h2>
        <p>Fokus kembangkan usaha, biar Maurekap yang urus rekapnya. Gratis 7 hari untuk merasakan sendiri bedanya.</p>
      </div>
      <div class="cta-right">
        <a href="#" class="btn btn-orange">Coba Gratis Sekarang <span class="arr">→</span></a>
        <small>Gratis 7 hari &nbsp;•&nbsp; Tanpa kartu kredit</small>
      </div>
    </div>
  </div>
</section>

</main>

<!-- ============ FOOTER ============ -->
<footer>
  <div class="container">
    <div class="foot-grid">
      <div class="foot-brand">
        <a class="logo d-flex align-items-center" href="#beranda" style="display: flex; align-items: center;">
          <span class="logo-badge" style="background:#fff; display: flex; align-items: center; justify-content: center;">
            <img src="{{ asset('img/logo/maurekap-icon-hd-transparent.png') }}" alt="Logo Maurekap" width="44" height="44" style="display:block;">
          </span>
          <span style="margin-left:10px;">MAUREKAP</span>
        </a>


        <p>Sistem rekap pembayaran untuk agen ekspedisi. Bantu usaha Anda lebih rapi, cepat, dan terkontrol.</p>
        <div class="socials">
          <a href="#" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3z"/></svg></a>
          <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6" fill="currentColor"/></svg></a>
          <a href="#" aria-label="X / Twitter"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 4l16 16M20 4L4 20"/></svg></a>
          <a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="6" width="19" height="12" rx="3.5"/><path d="M10.5 9.5l4.5 2.5-4.5 2.5z" fill="currentColor" stroke="none"/></svg></a>
        </div>
      </div>
      <div>
        <h4>PRODUK</h4>
        <ul>
          <li><a href="#keunggulan">Fitur</a></li>
          <li><a href="#cara-kerja">Cara Kerja</a></li>
          <li><a href="#">Harga</a></li>
          {{-- <li><a href="#testimoni">Testimoni</a></li> --}}
        </ul>
      </div>
      <div>
        <h4>BANTUAN</h4>
        <ul>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Panduan</a></li>
          <li><a href="#">Kebijakan Privasi</a></li>
          <li><a href="#">Syarat &amp; Ketentuan</a></li>
        </ul>
      </div>
      <div>
        <h4>HUBUNGI KAMI</h4>
        <ul class="contact">
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3z"/></svg><span>WhatsApp<br>0812-3456-7890</span></li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg><span>Email<br>support@maurekap.id</span></li>
        </ul>
      </div>
    </div>
    <div class="foot-bottom">© 2026 Maurekap. All rights reserved.</div>
  </div>
</footer>

<script>
(function(){
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* reveal on scroll */
  var io = new IntersectionObserver(function(es){
    es.forEach(function(e){
      if(!e.isIntersecting) return;
      e.target.classList.add('on');
      e.target.querySelectorAll('.count').forEach(runCount);
      if(e.target.classList.contains('count')) runCount(e.target);
      io.unobserve(e.target);
    });
  },{threshold:.15});
  document.querySelectorAll('[data-rv]').forEach(function(el){ io.observe(el); });

  /* angka dashboard menghitung naik */
  function runCount(el){
    if(el._done) return; el._done = 1;
    var target = parseFloat(el.dataset.count);
    if(reduce){ el.textContent = fmt(target); return; }
    var t0 = performance.now(), dur = 1600;
    (function tick(now){
      var p = Math.min((now - t0)/dur, 1), ease = 1 - Math.pow(1 - p, 3);
      el.textContent = fmt(Math.round(target * ease));
      if(p < 1) requestAnimationFrame(tick);
    })(t0);
  }
  function fmt(v){ return v.toLocaleString('id-ID'); }

  /* bayangan header saat scroll */
  var head = document.getElementById('header');
  window.addEventListener('scroll', function(){
    head.classList.toggle('scrolled', window.scrollY > 8);
  },{passive:true});

  /* tutup menu mobile saat link diklik */
  var tog = document.getElementById('nav-toggle');
  document.querySelectorAll('.nav a').forEach(function(a){
    a.addEventListener('click', function(){ tog.checked = false; });
  });
})();
</script>

</body>
</html>
