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

}
