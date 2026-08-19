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

    public function __construct($Nama, $Email, $Password, $LoginUrl)
    {
        $this->Nama = $Nama;
        $this->Email = $Email;
        $this->Password = $Password;
        $this->LoginUrl = $LoginUrl;
    }

    public function build()
    {
        return $this->subject('🎉 Pendaftaran Tenant Disetujui - Informasi Akun Anda')
            ->view('emails.tenant-approved');
    }
}
