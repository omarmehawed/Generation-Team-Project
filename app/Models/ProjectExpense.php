<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    protected $fillable = [
        'team_id', 'item_name', 'quantity', 'price', 'receipt_image', 'buyer_name',
        'component_id', 'price_per_unit',
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
