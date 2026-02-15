<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week_number',
        'commitment_level',
        'satisfaction_level',
        'general_notes',
        'pdf_path',
        'created_by',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(EvaluationItem::class)->orderBy('order');
    }

    // Helper to get items by type
    public function getTasksAttribute()
    {
        return $this->items->where('type', 'task');
    }

    public function getQuizzesAttribute()
    {
        return $this->items->where('type', 'quiz');
    }

    public function getMeetingsAttribute()
    {
        return $this->items->where('type', 'meeting');
    }

    public function getWorkshopsAttribute()
    {
        return $this->items->where('type', 'workshop');
    }

    public function getActivitiesAttribute()
    {
        return $this->items->where('type', 'activity');
    }
}
