<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSend extends Model
{
    protected $table = 'notification_sends';

    protected $fillable = ['user_id', 'notification_key', 'entity_id', 'scheduled_for'];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record that we sent (or are about to send) this notification to avoid duplicates.
     * Returns true if this is the first send for this combo; false if already sent.
     */
    public static function recordSend(int $userId, string $key, $entityId, \DateTimeInterface $scheduledFor): bool
    {
        $entityId = $entityId ?? 0;
        $exists = self::where('user_id', $userId)
            ->where('notification_key', $key)
            ->where('entity_id', $entityId)
            ->where('scheduled_for', $scheduledFor)
            ->exists();
        if ($exists) {
            return false;
        }
        self::create([
            'user_id' => $userId,
            'notification_key' => $key,
            'entity_id' => $entityId,
            'scheduled_for' => $scheduledFor,
        ]);
        return true;
    }
}
