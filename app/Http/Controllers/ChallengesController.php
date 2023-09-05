<?php

namespace App\Http\Controllers;

use App\Actions\Challenge\CreateChallengeAction;
use App\Actions\Challenge\StopChallengeAction;
use App\Http\Requests\CreateChallengeRequest;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use Illuminate\Validation\UnauthorizedException;

class ChallengesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $challenges = Challenge::query()
            ->latest('continued_at')
            ->byUserId(request()->user_id)
            ->paginate();

        return $this->ok(
            ChallengeResource::collection($challenges)
        );
    }

    public function store(CreateChallengeRequest $request, CreateChallengeAction $createChallengeAction)
    {
        $challenge = $createChallengeAction->execute($request->user(), $request->validated());

        return $this->created(
            ChallengeResource::make($challenge)
        );
    }

    public function destroy(Challenge $challenge, StopChallengeAction $stopChallengeAction)
    {
        if ($challenge->user_id !== request()->user()->id) {
            throw new UnauthorizedException();
        }

        $stopChallengeAction->execute($challenge);

        return $this->ok();
    }
}