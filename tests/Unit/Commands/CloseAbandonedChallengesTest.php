<?php

namespace Tests\Unit\Commands;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloseAbandonedChallengesTest extends TestCase
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

    public function test_abandoned_challenges_will_be_closed(): void
    {
        Challenge::factory()->create([
            'status' => ChallengeStatus::ONGOING->value,
            'continued_at' => now()->subDays(3)->subSeconds(),
        ]);

        Challenge::factory()->create([
            'status' => ChallengeStatus::ONGOING->value,
            'continued_at' => now()->subDays(2)->subSeconds(),
        ]);

        $this->artisan('app:close-abandoned-challenges');

        $abandonedChallengeCount = Challenge::where('status', ChallengeStatus::ABANDONED->value)->count();
        $ongoingChallengeCount = Challenge::where('status', ChallengeStatus::ONGOING->value)->count();
        $this->assertEquals(1, $abandonedChallengeCount);
        $this->assertEquals(1, $ongoingChallengeCount);
    }
}
