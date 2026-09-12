<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Akun Maurekap Anda Aktif</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Preview text (muncul di inbox preview) -->
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        Selamat! Akun Maurekap Anda sudah aktif. Login sekarang dengan kredensial di dalam email ini.
    </div>

    <!-- WRAPPER -->
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9;">
        <tr>
            <td align="center" style="padding: 32px 16px;">

                <!-- CONTAINER -->
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);">

                    <!-- ══════════ HEADER / BRAND BAR ══════════ -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); padding: 28px 32px; text-align: center;">
                            <div
                                style="display: inline-block; background: #ffffff; width: 44px; height: 44px; border-radius: 10px; line-height: 44px; color: #2563eb; font-weight: 800; font-size: 20px; font-family: Arial, sans-serif;">
                                M</div>
                            <div
                                style="color: #ffffff; font-size: 18px; font-weight: 700; margin-top: 8px; letter-spacing: 0.5px;">
                                MAUREKAP</div>
                        </td>
                    </tr>

                    <!-- ══════════ HERO / SUCCESS BADGE ══════════ -->
                    <tr>
                        <td style="padding: 40px 32px 24px; text-align: center;">
                            <div
                                style="display: inline-block; background-color: #dcfce7; color: #166534; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; margin-bottom: 16px;">
                                ✓ Akun Aktif
                            </div>

                            <h1
                                style="margin: 0 0 8px; font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1.2;">
                                Selamat, {{ $Nama ?? '-' }}! 🎉
                            </h1>
                            <p style="margin: 0; color: #64748b; font-size: 15px; line-height: 1.5;">
                                Pembayaran Anda telah kami terima dan akun Maurekap Anda sudah <strong
                                    style="color: #0f172a;">aktif dan siap digunakan</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- ══════════ KREDENSIAL LOGIN ══════════ -->
                    <tr>
                        <td style="padding: 0 32px 24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                                        <span
                                            style="display: inline-block; width: 28px; height: 28px; background: #dbeafe; color: #1e40af; border-radius: 7px; line-height: 28px; text-align: center; font-size: 14px; margin-right: 10px;">🔐</span>
                                        <span
                                            style="font-size: 13px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.8px;">Kredensial
                                            Login Anda</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px 24px 8px;">
                                        <div
                                            style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 600; margin-bottom: 4px;">
                                            Email / Username</div>
                                        <div
                                            style="font-size: 15px; color: #0f172a; font-weight: 600; word-break: break-all; font-family: 'Courier New', monospace; background: #ffffff; padding: 10px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                            {{ $Email ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 24px 20px;">
                                        <div
                                            style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 600; margin-bottom: 4px;">
                                            Password Sementara</div>
                                        <div
                                            style="font-size: 15px; color: #dc2626; font-weight: 700; word-break: break-all; font-family: 'Courier New', monospace; background: #ffffff; padding: 10px 12px; border-radius: 6px; border: 1px solid #fecaca; letter-spacing: 1px;">
                                            {{ $PasswordPlain ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ══════════ CTA BUTTON ══════════ -->
                    <tr>
                        <td style="padding: 0 32px 8px; text-align: center;">
                            <a href="{{ $LoginUrl ?? '#' }}"
                                style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #ffffff !important; text-decoration: none; padding: 14px 36px; border-radius: 10px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);">
                                Login ke Dashboard →
                            </a>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 12px;">
                                Atau salin link: <span
                                    style="color: #64748b; font-family: monospace;">{{ $LoginUrl ?? '-' }}</span>
                            </div>
                        </td>
                    </tr>

                    <!-- ══════════ DETAIL LANGGANAN (opsional) ══════════ -->
                    @if (!empty($PaketNama) || !empty($TanggalMulai) || !empty($TanggalBerakhir))
                        <tr>
                            <td style="padding: 24px 32px 8px;">
                                <div
                                    style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; margin-bottom: 12px;">
                                    Detail Langganan Anda</div>
                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                    style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 10px;">
                                    @if (!empty($PaketNama))
                                        <tr>
                                            <td
                                                style="padding: 12px 16px; border-bottom: 1px solid #dbeafe; font-size: 13px;">
                                                <span style="color: #64748b;">Paket</span>
                                                <span
                                                    style="float: right; font-weight: 700; color: #1e40af;">{{ $PaketNama ?? '-' }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($TanggalMulai))
                                        <tr>
                                            <td
                                                style="padding: 12px 16px; border-bottom: 1px solid #dbeafe; font-size: 13px;">
                                                <span style="color: #64748b;">Berlaku sejak</span>
                                                <span
                                                    style="float: right; font-weight: 600; color: #0f172a;">{{ $TanggalMulai ?? '-' }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($TanggalBerakhir))
                                        <tr>
                                            <td style="padding: 12px 16px; font-size: 13px;">
                                                <span style="color: #64748b;">Berakhir pada</span>
                                                <span
                                                    style="float: right; font-weight: 600; color: #0f172a;">{{ $TanggalBerakhir ?? '-' }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>
                    @endif

                    <!-- ══════════ LANGKAH SELANJUTNYA ══════════ -->
                    <tr>
                        <td style="padding: 24px 32px 8px;">
                            <div
                                style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; margin-bottom: 12px;">
                                Langkah Selanjutnya</div>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                style="margin-bottom: 10px;">
                                <tr>
                                    <td width="36" valign="top">
                                        <div
                                            style="width: 28px; height: 28px; background: #dbeafe; color: #1e40af; border-radius: 50%; line-height: 28px; text-align: center; font-size: 13px; font-weight: 800; font-family: Arial;">
                                            1</div>
                                    </td>
                                    <td valign="top" style="padding-left: 8px;">
                                        <div
                                            style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                                            Login & Lengkapi Profil Usaha</div>
                                        <div style="font-size: 13px; color: #64748b; line-height: 1.5;">Upload logo
                                            usaha, lengkapi detail outlet & jam operasional.</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                style="margin-bottom: 10px;">
                                <tr>
                                    <td width="36" valign="top">
                                        <div
                                            style="width: 28px; height: 28px; background: #dbeafe; color: #1e40af; border-radius: 50%; line-height: 28px; text-align: center; font-size: 13px; font-weight: 800; font-family: Arial;">
                                            2</div>
                                    </td>
                                    <td valign="top" style="padding-left: 8px;">
                                        <div
                                            style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                                            Ganti Password Segera</div>
                                        <div style="font-size: 13px; color: #64748b; line-height: 1.5;">Demi keamanan,
                                            ubah password sementara Anda di menu Profil.</div>
                                    </td>
                                </tr>
                            </table>


                        </td>
                    </tr>

                    <!-- ══════════ WARNING KEAMANAN ══════════ -->
                    <tr>
                        <td style="padding: 24px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 10px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="24" valign="top"
                                                    style="font-size: 18px; line-height: 1;">⚠️</td>
                                                <td valign="top" style="padding-left: 8px;">
                                                    <div
                                                        style="font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 4px;">
                                                        Perhatian Keamanan</div>
                                                    <div style="font-size: 12.5px; color: #78350f; line-height: 1.5;">
                                                        Jangan bagikan password Anda kepada siapapun. Tim Maurekap
                                                        <strong>tidak akan pernah</strong> meminta password Anda melalui
                                                        email, telepon, atau WhatsApp.
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ══════════ SUPPORT ══════════ -->
                    <tr>
                        <td style="padding: 0 32px 32px; text-align: center;">
                            <div style="font-size: 14px; color: #64748b; margin-bottom: 12px;">Butuh bantuan?</div>
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="padding: 0 8px;">
                                        <a href="mailto:support@maurekap.com"
                                            style="display: inline-block; background-color: #f1f5f9; color: #334155 !important; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; border: 1px solid #e2e8f0;">
                                            📧 Email Support
                                        </a>
                                    </td>
                                    <td style="padding: 0 8px;">
                                        <a href="https://wa.me/6285143671253"

                                            style="display: inline-block; background-color: #dcfce7; color: #166534 !important; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; border: 1px solid #bbf7d0;">
                                            💬 WhatsApp
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 12px;">
                                Senin — Sabtu · 08.00 — 20.00 WIB
                            </div>
                        </td>
                    </tr>

                    <!-- ══════════ FOOTER ══════════ -->
                    <tr>
                        <td style="background-color: #0f1b33; padding: 28px 32px; text-align: center;">
                            <div
                                style="color: #ffffff; font-size: 15px; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.5px;">
                                MAUREKAP</div>
                            <div style="color: #94a3b8; font-size: 12px; margin-bottom: 16px;">PT Maurekap Teknologi
                                Logistik</div>

                            <div style="margin-bottom: 16px;">
                                <a href="#"
                                    style="color: #cbd5e1; text-decoration: none; font-size: 12px; margin: 0 8px;">Website</a>
                                <span style="color: #475569;">·</span>
                                <a href="#"
                                    style="color: #cbd5e1; text-decoration: none; font-size: 12px; margin: 0 8px;">Instagram</a>
                                <span style="color: #475569;">·</span>
                                <a href="#"
                                    style="color: #cbd5e1; text-decoration: none; font-size: 12px; margin: 0 8px;">Kebijakan
                                    Privasi</a>
                            </div>

                            <div style="color: #64748b; font-size: 11px; line-height: 1.6;">
                                Email ini dikirim ke <strong style="color: #cbd5e1;">{{ $Email ?? '-' }}</strong>
                                karena Anda mendaftar sebagai mitra Maurekap.<br>
                                Mohon tidak membalas email ini. &copy; {{ date('Y') }} Maurekap. All rights
                                reserved.
                            </div>
                        </td>
                    </tr>

                </table>
                <!-- END CONTAINER -->

            </td>
        </tr>
    </table>

</body>

</html>
