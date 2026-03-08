<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFcmToken extends Model
{
    protected $fillable = ['user_id', 'token', 'device_type', 'locale'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
