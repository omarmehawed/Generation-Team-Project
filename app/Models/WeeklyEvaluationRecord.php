<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyEvaluationRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function period()
    {
        return $this->belongsTo(WeeklyEvaluationPeriod::class, 'evaluation_period_id');
    }

    public function evaluatee()
    {
        return $this->belongsTo(TeamMember::class, 'evaluatee_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(TeamMember::class, 'evaluator_id');
    }

    public function subItems()
    {
        return $this->hasMany(WeeklyEvaluationSubItem::class);
    }
}
