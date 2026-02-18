<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'date_of_birth',
        'national_id',
        'academic_id',
        'group',
        'phone_number',
        'whatsapp_number',

        'address',
        'is_dorm',
        'photo_path',
        'answers',
        'status',
        'user_id',
        'approved_by',
    ];

    protected $casts = [
        'answers' => 'array',
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
