<?php

namespace App\Support;

/**
 * Standardized notification data contract for database channel.
 * All notifications MUST use this structure in their toArray() for consistent Web + Flutter display.
 */
class NotificationPayload
{
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';

    public const ACTION_TYPE_DEEPLINK = 'deeplink';
    public const ACTION_TYPE_URL = 'url';
    public const ACTION_TYPE_NONE = 'none';

    public const AUDIENCE_SINGLE = 'single';
    public const AUDIENCE_SEGMENT = 'segment';
    public const AUDIENCE_BROADCAST = 'broadcast';

    public const CREATED_BY_SYSTEM = 'system';
    public const CREATED_BY_ADMIN = 'admin';

    /**
     * Build the canonical payload for storage in notifications.data.
     *
     * @param string $key Notification key (e.g. LIVE_CLASS_REMINDER_T_MINUS_10)
     * @param string $titleEn
     * @param string $titleAr
     * @param string $bodyEn
     * @param string $bodyAr
     * @param array $action ['type' => 'deeplink|url|none', 'value' => '...', 'meta' => []]
     * @param array|null $entity ['model' => '...', 'id' => ...]
     * @param string $priority low|normal|high
     * @param string $createdBy system|admin
     * @param string $audience single|segment|broadcast
     * @param array $meta Extra keys (course_id, class_id, etc.)
     * @return array
     */
    public static function build(
        string $key,
        string $titleEn,
        string $titleAr,
        string $bodyEn,
        string $bodyAr,
        array $action = ['type' => self::ACTION_TYPE_NONE, 'value' => '', 'meta' => []],
        ?array $entity = null,
        string $priority = self::PRIORITY_NORMAL,
        string $createdBy = self::CREATED_BY_SYSTEM,
        string $audience = self::AUDIENCE_SINGLE,
        array $meta = []
    ): array {
        $payload = [
            'key' => $key,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'body_en' => $bodyEn,
            'body_ar' => $bodyAr,
            'action' => array_merge(['type' => self::ACTION_TYPE_NONE, 'value' => '', 'meta' => []], $action),
            'priority' => $priority,
            'created_by' => $createdBy,
            'audience' => $audience,
            'meta' => $meta,
        ];
        if ($entity !== null) {
            $payload['entity'] = $entity;
        }
        // Backward compatibility: message for existing web/Flutter that expect 'message'
        $payload['message'] = $bodyEn;
        return $payload;
    }

    /**
     * Get display title for locale (ar/en).
     */
    public static function titleForLocale(array $data, string $locale = 'en'): string
    {
        if ($locale === 'ar' && !empty($data['title_ar'])) {
            return $data['title_ar'];
        }
        return $data['title_en'] ?? $data['message'] ?? 'Notification';
    }

    /**
     * Get display body for locale (ar/en).
     */
    public static function bodyForLocale(array $data, string $locale = 'en'): string
    {
        if ($locale === 'ar' && !empty($data['body_ar'])) {
            return $data['body_ar'];
        }
        return $data['body_en'] ?? $data['message'] ?? '';
    }
}
