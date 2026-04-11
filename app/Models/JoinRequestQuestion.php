<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinRequestQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'question_type',
        'options',
        'is_required',
        'order_priority',
        'conditional_logic',
        'placeholder',
        'section',
        'is_active',
        'archive_id',
    ];

    protected $casts = [
        'options' => 'array',
        'conditional_logic' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function archive()
    {
        return $this->belongsTo(QuestionArchive::class, 'archive_id');
    }
}
