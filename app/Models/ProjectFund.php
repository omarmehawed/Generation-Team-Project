<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFund extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'title', 'amount_per_member', 'deadline'];
    protected $guarded = [];
    // العلاقة مع المساهمات
    public function contributions()
    {
        return $this->hasMany(FundContribution::class, 'fund_id');
    }
    // علاقة التيم
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
