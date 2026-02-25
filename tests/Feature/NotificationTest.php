<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\NotificationPayload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_api_requires_auth(): void
    {
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(401);
    }

    public function test_notifications_index_returns_only_own(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $payload = NotificationPayload::build('TEST_KEY', 'Title', 'عنوان', 'Body', 'نص', [], null, 'normal', 'system', 'single', []);
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'Tests\Feature\DummyNotification',
            'data' => $payload,
        ]);
        $other->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'Tests\Feature\DummyNotification',
            'data' => $payload,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/notifications');
        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($user->notifications()->first()->id, $data[0]['id']);
    }

    public function test_unread_count_api(): void
    {
        $user = User::factory()->create();
        $payload = NotificationPayload::build('TEST_KEY', 'Title', 'عنوان', 'Body', 'نص', [], null, 'normal', 'system', 'single', []);
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'Tests\Feature\DummyNotification',
            'data' => $payload,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/notifications/unread-count');
        $response->assertOk()->assertJson(['count' => 1]);
    }

    public function test_mark_as_read_and_read_all(): void
    {
        $user = User::factory()->create();
        $payload = NotificationPayload::build('TEST_KEY', 'Title', 'عنوان', 'Body', 'نص', [], null, 'normal', 'system', 'single', []);
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'Tests\Feature\DummyNotification',
            'data' => $payload,
        ]);
        $id = $user->unreadNotifications()->first()->id;

        Sanctum::actingAs($user);
        $this->postJson("/api/notifications/{$id}/read")->assertOk();
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());

        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'Tests\Feature\DummyNotification',
            'data' => $payload,
        ]);
        $this->postJson('/api/notifications/read-all')->assertOk();
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }
}
