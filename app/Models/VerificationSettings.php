<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationSettings extends Model
{
    public const METHOD_WHATSAPP = 'whatsapp';
    public const METHOD_EMAIL    = 'email';
    public const METHOD_BOTH     = 'both';

    protected $fillable = [
        'method',
        'whatsapp_token',
        'whatsapp_phone_number_id',
        'whatsapp_otp_template',
        'whatsapp_api_version',
    ];

    /**
     * Get the singleton settings row, creating it with defaults on first call.
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'method'                   => self::METHOD_WHATSAPP,
            'whatsapp_token'           => null,
            'whatsapp_phone_number_id' => null,
            'whatsapp_otp_template'    => 'otp_verification',
            'whatsapp_api_version'     => 'v21.0',
        ]);
    }

    public function isEmailEnabled(): bool
    {
        return in_array($this->method, [self::METHOD_EMAIL, self::METHOD_BOTH], true);
    }

    public function isWhatsappEnabled(): bool
    {
        return in_array($this->method, [self::METHOD_WHATSAPP, self::METHOD_BOTH], true);
    }
}
