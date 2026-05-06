<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_response_id',
        'form_question_id',
        'answer_text',
        'answer_json',
        'answer_file',
    ];

    protected $casts = [
        'answer_json' => 'array',
    ];

    public function response()
    {
        return $this->belongsTo(FormResponse::class, 'form_response_id');
    }

    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'form_question_id');
    }
}
