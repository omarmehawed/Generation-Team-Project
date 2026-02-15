<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Log a system activity.
     *
     * @param string $action The action key (e.g., 'task_assigned')
     * @param string|null $description Human formatted description
     * @param Model|null $subject The subject model (Task, Team, etc.)
     * @param int|null $teamId The team ID (User's journey context)
     * @param int|null $targetUserId The recipient of the action (User B)
     * @param array $properties Extra metadata (old/new values)
     * @return ActivityLog
     */
    public static function log(
        string $action, 
        ?string $description = null, 
        ?Model $subject = null, 
        ?int $teamId = null, 
        ?int $targetUserId = null, 
        array $properties = []
    )
    {
        // Try to infer team_id if not provided
        if (!$teamId && $subject && property_exists($subject, 'team_id')) {
            $teamId = $subject->team_id;
        }

        return ActivityLog::create([
            'causer_id'      => Auth::id(), // Null if system/cron
            'subject_id'     => $subject ? $subject->id : null,
            'subject_type'   => $subject ? get_class($subject) : null,
            'team_id'        => $teamId,
            'target_user_id' => $targetUserId,
            'action'         => $action,
            'description'    => $description,
            'changes'        => $properties,
        ]);
    }
}
