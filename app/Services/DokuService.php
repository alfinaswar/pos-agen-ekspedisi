<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DokuService
{
    protected string $clientId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.doku.client_id');
        $this->secretKey = config('services.doku.secret_key');
        $this->baseUrl = config('services.doku.base_url');
    }

    /**
     * Create checkout payment
     */
    public function createCheckout(array $params): array
    {
        $requestId = (string) Str::uuid();
        // Base endpoint untuk DOKU pembayaran
        $requestTarget = '/checkout/v1/payment';

        // Untuk production:
        // $requestTarget = '/checkout/v1/payment'; // Biasanya sama, hanya baseUrl yang beda
        $timestamp = Carbon::now('UTC')->format('Y-m-d\TH:i:s\Z');

        $body = [
            'order' => [
                'amount' => (int) $params['amount'],
                'invoice_number' => $params['invoice_number'],
                'callback_url' => $params['callback_url'] ?? null,
                'auto_redirect' => $params['auto_redirect'] ?? true,
            ],
            'payment' => [
                'payment_due_date' => $params['payment_due_date'] ?? 60,
            ],
            'customer' => [
                'id' => $params['customer_id'] ?? null,
                'name' => $params['customer_name'],
                'email' => $params['customer_email'],
                'phone' => $params['customer_phone'] ?? null,
            ],
        ];

        // 🔥 TAMBAHAN: return_url untuk DOKU versi baru
        if (!empty($params['return_url'])) {
            $body['order']['return_url'] = $params['return_url'];
        }

        // 🔥 TAMBAHAN: auto_redirect_return_url
        if (isset($params['auto_redirect_return_url'])) {
            $body['order']['auto_redirect_return_url'] = $params['auto_redirect_return_url'];
        }

        if (!empty($params['notification_url'])) {
            $body['additional_info'] = [
                'override_notification_url' => $params['notification_url'],
            ];
        }

        $jsonBody = json_encode($body);
        $signature = $this->generateSignature($requestId, $timestamp, $requestTarget, $jsonBody);

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . $requestTarget, $body);

        Log::info('DOKU createCheckout', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json() ?? [],
            'raw' => $response->body(),
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
        $digest = base64_encode(hash('sha256', $jsonBody, true));

        $signatureComponent = implode("\n", [
            "Client-Id:{$this->clientId}",
            "Request-Id:{$requestId}",
            "Request-Timestamp:{$timestamp}",
            "Request-Target:{$requestTarget}",
            "Digest:{$digest}",
        ]);

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
        string $notificationTarget,
        string $rawBody,
        string $incomingSignature
    ): bool {
        if ($clientId !== $this->clientId) {
            Log::warning('DOKU Client-Id mismatch', [
                'expected' => $this->clientId,
                'received' => $clientId,
            ]);
            return false;
        }

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

    /**
     * Normalisasi nomor telepon ke format E.164 (+62...)
     */
    public function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        if (empty($cleaned)) {
            return '';
        }

        if (str_starts_with($cleaned, '+62')) {
            return $cleaned;
        }

        if (str_starts_with($cleaned, '62')) {
            return '+' . $cleaned;
        }

        if (str_starts_with($cleaned, '0')) {
            return '+62' . substr($cleaned, 1);
        }

        if (preg_match('/^8[0-9]{8,11}$/', $cleaned)) {
            return '+62' . $cleaned;
        }

        return '+62' . $cleaned;
    }

    /**
     * 🔥 NEW: Cek status pembayaran di DOKU
     * Endpoint: GET /checkout/v1/payment/{invoice_number}
     */
    public function checkPaymentStatus(string $invoiceNumber): array
    {
        $requestId = (string) Str::uuid();
        $requestTarget = '/orders/v1/status/' . rawurlencode($invoiceNumber);
        $timestamp = Carbon::now('UTC')->format('Y-m-d\TH:i:s\Z');

        // GET request → TIDAK pakai Digest
        $signatureComponent = implode("\n", [
            "Client-Id:{$this->clientId}",
            "Request-Id:{$requestId}",
            "Request-Timestamp:{$timestamp}",
            "Request-Target:{$requestTarget}",
        ]);

        $hmac = base64_encode(
            hash_hmac('sha256', $signatureComponent, $this->secretKey, true)
        );

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => "HMACSHA256={$hmac}",
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . $requestTarget);

        Log::info('DOKU checkPaymentStatus', [
            'invoice' => $invoiceNumber,
            'http' => $response->status(),
            'body' => $response->json(),
        ]);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json() ?? [],
            'raw' => $response->body(),
        ];
    }
}
