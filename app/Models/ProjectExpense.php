<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    protected $fillable = [
        'team_id', 
        'user_id',
        'component_id',
        'item',
        'shop_name',
        'price_per_unit',
        'quantity',
        'amount',
        'receipt_path',
    ];

    public function contributions()
    {
        return $this->hasMany(\App\Models\ExpenseContribution::class);
    }

    public function component()
    {
        return $this->belongsTo(ProjectComponent::class);
    }
    // علاقة التيم
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
