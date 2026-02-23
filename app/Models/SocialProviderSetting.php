<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialProviderSetting extends Model
{
    protected $fillable = [
        'provider',
        'enabled',
        'client_id',
        'client_secret',
        'redirect_uri',
        'extra',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'extra' => 'array',
    ];

    public const PROVIDER_GOOGLE = 'google';
    public const PROVIDER_TWITTER = 'twitter';

    public static function getForProvider(string $provider): ?self
    {
        return self::where('provider', $provider)->first();
    }

    public function getExtraValue(string $key): mixed
    {
        return is_array($this->extra) ? ($this->extra[$key] ?? null) : null;
    }
}
