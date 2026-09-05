<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokuNotification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doku_notifications';
    protected $fillable = [
        'invoice_number',
        'transaction_status',
        'payment_channel',
        'amount',
        'raw_payload',
        'signature',
        'signature_valid',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'signature_valid' => 'boolean',
        'amount' => 'decimal:2',
    ];
}
