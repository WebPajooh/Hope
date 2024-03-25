<?php

namespace Tests\Unit\Commands;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeleteReadNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Country::create([
            'code' => 'GB',
            'name' => 'United Kingdom',
        ]);

        $this->signIn();
    }

    public function test_read_notifications_will_be_deleted_after_months(): void
    {
        DB::table('notifications')
            ->insert([
                'id' => 'xyz',
                'type' => 'xyz',
                'notifiable_id' => 1,
                'notifiable_type' => 'xyz',
                'data' => 'xyz',
                'read_at' => now()->subMonths(3),
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(3),
            ]);

        $this->artisan('app:delete-seen-notifications');

        $this->assertDatabaseEmpty('notifications');
    }
}
