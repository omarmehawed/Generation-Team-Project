<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_at'            => 'datetime',
        'end_at'              => 'datetime',
        'require_fullscreen'  => 'boolean',
        'shuffle_questions'   => 'boolean',
        'shuffle_options'     => 'boolean',
        'auto_cancel_on_copy' => 'boolean',
        'auto_cancel_on_paste'=> 'boolean',
        'is_published'        => 'boolean',
        'targeted_roles'      => 'array',
    ];

    public function isAvailableFor(User $user)
    {
        // Admins can always see and test any quiz
        if ($user->hasPermission('manage_quizzes')) {
            return true;
        }

        $roles = is_string($this->targeted_roles) ? json_decode($this->targeted_roles, true) : $this->targeted_roles;
        $roles = $roles ?? [];

        if (empty($roles)) {
            return true;
        }

        if (in_array('all', $roles)) {
            return true;
        }

        $memberships = $user->teamMemberships;

        foreach ($roles as $target) {
            $conditions = explode('|', $target);
            $matched = false;

            foreach ($memberships as $membership) {
                $membershipMatches = true;
                foreach ($conditions as $condition) {
                    $parts = explode(':', $condition);
                    if (count($parts) !== 2) continue;
                    [$type, $value] = $parts;

                    if ($type === 'tech' && strtolower((string)$membership->technical_role) !== strtolower($value)) {
                        $membershipMatches = false;
                        break;
                    }
                    if ($type === 'role') {
                        if ($value === 'sub_leader' && !$membership->is_sub_leader) {
                            $membershipMatches = false;
                            break;
                        }
                        if ($value === 'vice_leader' && !$membership->is_vice_leader) {
                            $membershipMatches = false;
                            break;
                        }
                    }
                }
                if ($membershipMatches) {
                    $matched = true;
                    break;
                }
            }

            if ($matched) {
                return true;
            }
        }

        return false;
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
