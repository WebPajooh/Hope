<?php

namespace Tests\Feature;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContinueChallengeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_should_wait_a_day_to_continue_the_challenge()
    {
        Challenge::factory()->create([
            'status' => ChallengeStatus::ONGOING->value,
            'continued_at' => now()->subDay()->addMinute(),
            'created_at' => now()->subDay()->addMinute(),
        ]);

        $this->postJson('api/challenges/1/continue')
            ->assertStatus(400);
    }

    public function test_user_cant_continue_non_ongoing_challenges()
    {
        Challenge::factory()->create([
            'status' => ChallengeStatus::STOPPED->value,
        ]);

        $this->postJson('api/challenges/1/continue')
            ->assertStatus(400);
    }

    public function test_user_cant_continue_others_challenges()
    {
        User::factory()->create();

        Challenge::factory()->create([
            'user_id' => 2,
            'status' => ChallengeStatus::ONGOING->value,
        ]);

        $this->postJson('api/challenges/1/continue')
            ->assertStatus(403);
    }

    public function test_user_continues_the_challenge()
    {
        Challenge::factory()->create([
            'status' => ChallengeStatus::ONGOING->value,
            'continued_at' => now()->subDays(3),
            'created_at' => now()->subDays(3),
        ]);

        $this->postJson('api/challenges/1/continue')
            ->assertStatus(200);

        $challenge = Challenge::first();
        $this->assertTrue($challenge->continued_at->diffInDays() < 1);
        $this->assertTrue($challenge->updated_at->diffInDays() > 0);
    }

    public function test_user_completes_a_challenge()
    {
        Notification::fake();

        Challenge::factory()->create([
            'status' => ChallengeStatus::ONGOING->value,
            'continued_at' => '2022-01-01 21:00:00',
            'created_at' => '2022-01-01 21:00:00',
        ]);

        $this->travelTo('2023-01-31 21:01:00');

        $this->postJson('api/challenges/1/continue');

        $this
            ->getJson('api/users/1/achievements')
            ->assertJson([
                'data' => [
                    [
                        'name' => 'hero',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('challenges', [
            'status' => ChallengeStatus::COMPLETED->value,
        ]);

        Notification::assertCount(1);
    }
}
