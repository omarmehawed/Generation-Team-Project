<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    //  ضيف 'description' و 'meeting_link' و 'location' هنا ضروري
    protected $fillable = [
        'team_id',
        'topic',
        'description', // <--- دي اللي كانت ناقصة غالباً
        'meeting_date',
        'mode',
        'status',
        'meeting_link',
        'location',
        'meeting_link',
        'type',
        'notes'
    ];

    protected $casts = ['meeting_date' => 'datetime'];

    public function attendances()
    {
        return $this->hasMany(MeetingAttendance::class, 'meeting_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
