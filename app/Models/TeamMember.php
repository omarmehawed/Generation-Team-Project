<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    // السطر ده هو الحل (بيسمح بإدخال البيانات)
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'technical_role',
        'extra_role',
        'rank',
        'is_group_a',
        'is_group_b',
        'sub_team',
        'is_vice_leader',
        'parent_id',
        'team_number',
        'is_sub_leader',
        'status', // Added status
        'can_manage_components',
        'can_manage_expenses',
        'can_access_join_requests',
        'can_manage_quizzes',
    ];

    protected $casts = [
        'can_manage_components' => 'boolean',
        'can_manage_expenses' => 'boolean',
        'can_access_join_requests' => 'boolean',
        'can_manage_quizzes' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($teamMember) {
            // Only assign funds if the member is active
            if (!in_array($teamMember->status, ['pending', 'rejected'])) {
                // 1. Restore any soft-deleted contributions for this user in this team
                $trashed = \App\Models\FundContribution::onlyTrashed()
                    ->where('user_id', '=', $teamMember->user_id)
                    ->whereHas('fund', function($q) use ($teamMember) {
                        $q->where('team_id', '=', $teamMember->team_id);
                    })->get();
                
                foreach ($trashed as $t) {
                    $t->restore();
                }

                // 2. Assign any new funds they missed while they were gone (or initially)
                $funds = \App\Models\ProjectFund::where('team_id', '=', $teamMember->team_id)->get();
                foreach ($funds as $fund) {
                    $exists = \App\Models\FundContribution::where('fund_id', '=', $fund->id)
                                ->where('user_id', '=', $teamMember->user_id)
                                ->exists();
                    if (!$exists) {
                        \App\Models\FundContribution::create([
                            'fund_id' => $fund->id,
                            'user_id' => $teamMember->user_id,
                            'status' => 'pending'
                        ]);
                    }
                }
            }
        });

        static::deleted(function ($teamMember) {
            // Soft delete unpaid fund contributions when the user is removed from the team
            // (Paid ones remain to keep team collected amounts accurate)
            \App\Models\FundContribution::where('user_id', '=', $teamMember->user_id)
                ->where('status', '!=', 'paid')
                ->whereHas('fund', function($q) use ($teamMember) {
                    $q->where('team_id', '=', $teamMember->team_id);
                })->delete();
        });
    }
}
