<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranTenant;
use App\Models\DokuNotification;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function handle(Request $request, DokuService $doku)
    {
        $rawBody = $request->getContent();
        $headers = $request->headers;

        $clientId = $headers->get('Client-Id', '');
        $requestId = $headers->get('Request-Id', '');
        $timestamp = $headers->get('Request-Timestamp', '');
        $signature = $headers->get('Signature', '');

        // Path webhook kita (tanpa domain)
        $notificationTarget = '/' . ltrim($request->path(), '/');

        // Verify signature
        $isValid = $doku->verifyNotificationSignature(
            $clientId,
            $requestId,
            $timestamp,
            $notificationTarget,
            $rawBody,
            $signature
        );

        $payload = json_decode($rawBody, true) ?? [];

        // Log notifikasi
        DokuNotification::create([
            'invoice_number' => $payload['order']['invoice_number'] ?? 'UNKNOWN',
            'transaction_status' => $payload['transaction']['status'] ?? null,
            'payment_channel' => $payload['channel']['id'] ?? ($payload['payment']['identifier'][0]['type'] ?? null),
            'amount' => $payload['order']['amount'] ?? 0,
            'raw_payload' => $payload,
            'signature' => $signature,
            'signature_valid' => $isValid,
        ]);

        if (!$isValid) {
            Log::warning('DOKU Webhook: Invalid signature', [
                'invoice' => $payload['order']['invoice_number'] ?? null,
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // Cari pendaftaran berdasarkan invoice number
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;
        $status = $payload['transaction']['status'] ?? null;

        if ($invoiceNumber) {
            $pendaftaran = PendaftaranTenant::where('DokuInvoiceNumber', $invoiceNumber)->first();

            if ($pendaftaran) {
                // Map DOKU status ke status internal
                $paymentStatus = match (strtoupper($status)) {
                    'SUCCESS' => 'PAID',
                    'FAILED' => 'FAILED',
                    'EXPIRED' => 'EXPIRED',
                    default => $pendaftaran->PaymentStatus,
                };

                $updateData = ['PaymentStatus' => $paymentStatus];

                if ($paymentStatus === 'PAID') {
                    $updateData['PaidAt'] = now();
                    $updateData['PaymentChannel'] = $payload['channel']['id']
                        ?? ($payload['payment']['identifier'][0]['type'] ?? null);
                }

                $pendaftaran->update($updateData);

                Log::info("DOKU Webhook: {$invoiceNumber} => {$paymentStatus}");
            }
        }

        // DOKU expects 200 response
        return response()->json(['message' => 'OK'], 200);
    }
}
