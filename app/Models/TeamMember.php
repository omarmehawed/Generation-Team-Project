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
                $funds = \App\Models\ProjectFund::where('team_id', $teamMember->team_id)->get();
                foreach ($funds as $fund) {
                    \App\Models\FundContribution::firstOrCreate([
                        'fund_id' => $fund->id,
                        'user_id' => $teamMember->user_id,
                    ], [
                        'status' => 'pending'
                    ]);
                }
            }
        });

        static::deleted(function ($teamMember) {
            // Delete unpaid fund contributions when the user is removed from the team
            \App\Models\FundContribution::where('user_id', $teamMember->user_id)
                ->whereIn('status', ['pending', 'pending_approval'])
                ->whereHas('fund', function($q) use ($teamMember) {
                    $q->where('team_id', $teamMember->team_id);
                })->delete();
        });
    }
}
