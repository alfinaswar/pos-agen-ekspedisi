@component('mail::layout')

{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
MAUREKAP
@endcomponent
@endslot

{{-- Greeting --}}
# Halo, {{ $user->name ?? 'Pengguna' }}! 👋

{{-- Intro --}}
Kami menerima permintaan untuk mereset password akun MAUREKAP Anda.

<div class="alert-box" style="background: #f7fafd; border-left: 4px solid #2a5298; border-radius: 8px; padding: 16px 20px; margin: 20px 0;">
    <p style="margin: 0; color: #223248; font-size: 14px;">
        <strong>⚠️ Penting:</strong> Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.
    </p>
</div>

{{-- Tombol Reset --}}
@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
🔐 Reset Password Saya
@endcomponent

{{-- Info tambahan --}}
<p style="color: #7f8fa6; font-size: 14px; margin-top: 20px;">
    Tautan di atas hanya berlaku selama <strong>60 menit</strong>. Jika sudah kadaluarsa, Anda perlu meminta tautan baru melalui halaman login.
</p>

<p style="color: #7f8fa6; font-size: 14px;">
    Jika tombol tidak berfungsi, salin tautan di bawah ini ke browser Anda:
</p>

<div class="code-block" style="background: #f3f5fa; border: 1.5px solid #dde7f0; border-radius: 10px; padding: 12px 16px; word-break: break-all; font-size: 13px; color: #2a5298;">
    {{ $actionUrl }}
</div>

{{-- Outro --}}
Terima kasih,<br>
<strong style="color: #1e3c72;">Tim MAUREKAP</strong>
<br>
<span style="font-size: 13px; color: #7f8fa6;">Sistem Pencatatan Pendapatan & Laporan</span>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} POS Agen Ekspedisi. All rights reserved.
@endcomponent
@endslot

@endcomponent
