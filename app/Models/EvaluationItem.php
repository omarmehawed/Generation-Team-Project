<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_evaluation_id',
        'type', // task, quiz, meeting, workshop, activity
        'title',
        'rating',
        'mark',
        'note',
        'order',
    ];

    public function evaluation()
    {
        return $this->belongsTo(WeeklyEvaluation::class, 'weekly_evaluation_id');
    }
}
