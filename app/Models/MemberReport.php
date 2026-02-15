<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    // العلاقة مع التيم
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}