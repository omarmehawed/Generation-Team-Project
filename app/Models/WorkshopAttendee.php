<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkshopAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'user_id',
        'status', // attended, absent, late, pending
        'participation_score',
        'files_uploaded',
    ];

    protected $casts = [
        'files_uploaded' => 'array',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
