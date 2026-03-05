<?php

namespace App\Services;

use App\Models\TeamMember;
use App\Notifications\BatuNotification;
use Illuminate\Support\Facades\Auth;

/**
 * Helper to send BatuNotification to all (or specific) team members.
 */
class TeamNotifier
{
    /**
     * Notify all team members, optionally excluding certain user IDs.
     *
     * @param  int|object  $team          Team model or team_id
     * @param  array       $data          Notification payload
     * @param  array       $excludeIds    User IDs to skip (e.g. the actor)
     */
    public static function notifyAll($team, array $data, array $excludeIds = []): void
    {
        $teamId = is_object($team) ? $team->id : $team;

        TeamMember::with('user')
            ->where('team_id', $teamId)
            ->where('status', 'approved')
            ->whereNotIn('user_id', $excludeIds)
            ->get()
            ->each(function ($member) use ($data) {
                if ($member->user) {
                    $member->user->notify(new BatuNotification($data));
                }
            });
    }

    /**
     * Notify only the leader(s) of a team.
     */
    public static function notifyLeaders($team, array $data, array $excludeIds = []): void
    {
        $teamId = is_object($team) ? $team->id : $team;

        TeamMember::with('user')
            ->where('team_id', $teamId)
            ->whereIn('role', ['leader', 'vice_leader'])
            ->where('status', 'approved')
            ->whereNotIn('user_id', $excludeIds)
            ->get()
            ->each(function ($member) use ($data) {
                if ($member->user) {
                    $member->user->notify(new BatuNotification($data));
                }
            });
    }
}
