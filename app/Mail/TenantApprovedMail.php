<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $Nama;
    public $Email;
    public $Password;
    public $LoginUrl;

    // ✅ Opsional: detail langganan (boleh tidak diisi)
    public $PaketNama;
    public $TanggalMulai;
    public $TanggalBerakhir;

    public function __construct(
        $Nama = null,
        $Email = null,
        $Password = null,
        $LoginUrl = null,
        $PaketNama = null,
        $TanggalMulai = null,
        $TanggalBerakhir = null
    ) {
        $this->Nama = $Nama ?? '';
        $this->Email = $Email ?? '';
        $this->Password = $Password ?? '';
        $this->LoginUrl = $LoginUrl ?? '';
        $this->PaketNama = $PaketNama ?? '';
        $this->TanggalMulai = $TanggalMulai ?? '';
        $this->TanggalBerakhir = $TanggalBerakhir ?? '';
    }

    public function build()
    {
        return $this->subject('🎉 Akun Maurekap Anda Telah Aktif — Selamat Bergabung!')
            ->view('emails.tenant-approved');
    }
}
