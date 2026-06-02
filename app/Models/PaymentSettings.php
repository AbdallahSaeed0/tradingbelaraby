<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettings extends Model
{
    protected $fillable = [
        'bank_transfer_enabled',
        'bank_transfer_bank_name',
        'bank_transfer_account_name',
        'bank_transfer_account_number',
        'bank_transfer_iban',
        'bank_transfer_instructions',
    ];

    protected $casts = [
        'bank_transfer_enabled' => 'boolean',
    ];

    /**
     * Get the payment settings singleton (creates with defaults if none exist).
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'bank_transfer_enabled' => false,
        ]);
    }
}
