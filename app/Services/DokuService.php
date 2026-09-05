<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DokuService
{
    protected string $clientId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId  = config('services.doku.client_id');
        $this->secretKey = config('services.doku.secret_key');
        $this->baseUrl   = config('services.doku.base_url');
    }

    /**
     * Create checkout payment
     */
    public function createCheckout(array $params): array
    {
        $requestId     = (string) Str::uuid();
        $requestTarget = '/checkout/v1/payment';
        $timestamp     = Carbon::now('UTC')->format('Y-m-d\TH:i:s\Z');

        $body = [
            'order' => [
                'amount'         => (int) $params['amount'],
                'invoice_number' => $params['invoice_number'],
                'callback_url'   => $params['callback_url'] ?? url('/pendaftaran/sukses'),
                'auto_redirect'  => true,
            ],
            'payment' => [
                'payment_due_date' => $params['payment_due_date'] ?? 60, // menit
            ],
            'customer' => [
                'id'    => $params['customer_id'] ?? null,
                'name'  => $params['customer_name'],
                'email' => $params['customer_email'],
                'phone' => $params['customer_phone'] ?? null,
            ],
        ];

        // Jika ingin override notification URL per-transaksi
        if (!empty($params['notification_url'])) {
            $body['additional_info'] = [
                'override_notification_url' => $params['notification_url'],
            ];
        }

        $jsonBody  = json_encode($body);
        $signature = $this->generateSignature($requestId, $timestamp, $requestTarget, $jsonBody);

        $response = Http::withHeaders([
            'Client-Id'         => $this->clientId,
            'Request-Id'        => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature'         => $signature,
            'Content-Type'      => 'application/json',
        ])->post($this->baseUrl . $requestTarget, $body);

        return [
            'success'  => $response->successful(),
            'status'   => $response->status(),
            'body'     => $response->json(),
            'raw'      => $response->body(),
        ];
    }

    /**
     * Generate HMAC-SHA256 signature for request
     */
    public function generateSignature(
        string $requestId,
        string $timestamp,
        string $requestTarget,
        string $jsonBody
    ): string {
        // 1. Generate Digest = SHA256 base64 of body
        $digest = base64_encode(hash('sha256', $jsonBody, true));

        // 2. Build signature component string
        $signatureComponent = implode("\n", [
            "Client-Id:{$this->clientId}",
            "Request-Id:{$requestId}",
            "Request-Timestamp:{$timestamp}",
            "Request-Target:{$requestTarget}",
            "Digest:{$digest}",
        ]);

        // 3. HMAC-SHA256 with secret key
        $hmac = base64_encode(
            hash_hmac('sha256', $signatureComponent, $this->secretKey, true)
        );

        return "HMACSHA256={$hmac}";
    }

    /**
     * Verify notification signature from DOKU
     */
    public function verifyNotificationSignature(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $notificationTarget, // path webhook kita, misal /webhooks/doku
        string $rawBody,
        string $incomingSignature
    ): bool {
        $digest = base64_encode(hash('sha256', $rawBody, true));

        $signatureComponent = implode("\n", [
            "Client-Id:{$clientId}",
            "Request-Id:{$requestId}",
            "Request-Timestamp:{$timestamp}",
            "Request-Target:{$notificationTarget}",
            "Digest:{$digest}",
        ]);

        $expectedHmac = base64_encode(
            hash_hmac('sha256', $signatureComponent, $this->secretKey, true)
        );

        $expectedSignature = "HMACSHA256={$expectedHmac}";

        return hash_equals($expectedSignature, $incomingSignature);
    }
    // app/Services/DokuService.php

    /**
     * Normalisasi nomor telepon ke format E.164 (+62...)
     */
    public function normalizePhone(string $phone): string
    {
        // Hapus semua karakter kecuali angka dan +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        // Jika kosong, return empty
        if (empty($cleaned)) {
            return '';
        }

        // Jika sudah dimulai dengan +62, langsung return
        if (str_starts_with($cleaned, '+62')) {
            return $cleaned;
        }

        // Jika dimulai dengan 62 (tanpa +), tambahkan +
        if (str_starts_with($cleaned, '62')) {
            return '+' . $cleaned;
        }

        // Jika dimulai dengan 0, ganti dengan +62
        if (str_starts_with($cleaned, '0')) {
            return '+62' . substr($cleaned, 1);
        }

        // Jika hanya angka biasa (anggap Indonesia), tambahkan +62
        if (preg_match('/^8[0-9]{8,11}$/', $cleaned)) {
            return '+62' . $cleaned;
        }

        // Fallback: return apa adanya dengan +62
        return '+62' . $cleaned;
    }
}
