<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'created_by',
        'title',
        'workshop_date',
        'workshop_time',
        'type', // online, offline
        'location_or_link',
        'domain', // software, hardware, general
    ];

    protected $casts = [
        'workshop_date' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees()
    {
        return $this->hasMany(WorkshopAttendee::class);
    }
}
