<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'started_at'           => 'datetime',
        'ends_at'              => 'datetime',
        'submitted_at'         => 'datetime',
        'last_activity_at'     => 'datetime',
        'extra_time_ends_at'   => 'datetime',
        'extra_time_granted_at'=> 'datetime',
        'score'                => 'decimal:2',
        'question_order'       => 'array',
    ];

    public function extraTimeGranter()
    {
        return $this->belongsTo(User::class, 'extra_time_granted_by');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    public function violations()
    {
        return $this->hasMany(QuizViolation::class, 'attempt_id')->orderBy('created_at', 'desc');
    }

    public function retryRequest()
    {
        return $this->hasOne(QuizRetryRequest::class, 'attempt_id');
    }
}
