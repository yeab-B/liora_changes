<?php

namespace App\Services;

use App\Models\User;
use App\Shared\Enums\ChallengeStatus;

class StreakService
{
    /**
     * Calculate streak changes based on a completed task.
     */
    public function incrementStreak(int $currentStreak, int $longestStreak): array
    {
        $newCurrent = $currentStreak + 1;
        $newLongest = $newCurrent > $longestStreak ? $newCurrent : $longestStreak;
        
        return [
            'current_streak' => $newCurrent,
            'longest_streak' => $newLongest
        ];
    }

    /**
     * Break the streak when a task is skipped or missed.
     */
    public function breakStreak(): int
    {
        return 0; // The current streak resets to 0.
    }

    /**
     * Recompute the user-level current_streak/longest_streak fields (exposed
     * by GET /me and GET /dashboard per docs/mvp/05-api-contract.md §1.4/§4.1)
     * from the per-challenge streaks. A user can run several challenges at
     * once, each with its own streak, so these aggregate as: current_streak =
     * best streak among currently-active challenges, longest_streak = best
     * streak the user has ever reached on any challenge. Called after every
     * check-in (complete or skip) so both fields stay in sync with whichever
     * challenge just changed.
     */
    public function syncUserStreaks(User $user): void
    {
        $user->current_streak = (int) $user->challenges()
            ->where('status', ChallengeStatus::Active->value)
            ->max('current_streak');

        $user->longest_streak = (int) $user->challenges()->max('longest_streak');

        $user->save();
    }
}
