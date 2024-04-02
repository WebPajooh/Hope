<?php

namespace App\Actions\Challenge;

use App\Enums\ChallengeStatus;
use App\Events\ChallengeContinued;
use App\Models\Challenge;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ContinueChallengeAction
{
    public function execute(Challenge $challenge): Challenge
    {
        if ($challenge->status !== ChallengeStatus::ONGOING->value) {
            throw new HttpException(400, 'This challenge is not ongoing!');
        }

        if ($challenge->continued_at->diffInDays() < 1) {
            throw new HttpException(400, 'Not a day has passed yet!');
        }

        $challenge->timestamps = false;
        $challenge->continued_at = now();

        if ($challenge->passedDays() === 30) {
            $challenge->status = ChallengeStatus::COMPLETED->value;
        }

        $challenge->save();

        ChallengeContinued::dispatch($challenge);

        return $challenge;
    }
}
