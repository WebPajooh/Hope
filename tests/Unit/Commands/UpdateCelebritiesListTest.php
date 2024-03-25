<?php

namespace Tests\Unit\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCelebritiesListTest extends TestCase
{
    use RefreshDatabase;

    public function test_most_followed_users_get_an_achievement()
    {
        $setOfUsers = User::factory()->count(5)->create();

        $this->user
            ->followers()
            ->attach($setOfUsers->pluck('id'));

        $this->artisan('app:update-celebrities-list');

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => 1,
        ]);
    }
}
