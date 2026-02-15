<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'causer_id', 
        'subject_id', 
        'subject_type',
        'team_id',
        'target_user_id',
        'action', 
        'description',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // Actor
    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id')->withTrashed();
    }

    // Polymorphic Subject (Task, File, etc.)
    public function subject()
    {
        return $this->morphTo();
    }
    
    // Target User (User B)
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id')->withTrashed();
    }

    // Team Context
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
