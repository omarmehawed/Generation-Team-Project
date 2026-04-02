<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyEvaluationPeriod extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function records()
    {
        return $this->hasMany(WeeklyEvaluationRecord::class, 'evaluation_period_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
