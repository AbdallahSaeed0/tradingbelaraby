<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualNotificationCampaign extends Model
{
    protected $fillable = [
        'admin_id', 'audience_type', 'audience_filter',
        'title_ar', 'title_en', 'body_ar', 'body_en',
        'action_json', 'entity_json', 'priority',
        'scheduled_at', 'sent_at', 'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'audience_filter' => 'array',
        'action_json' => 'array',
        'entity_json' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
