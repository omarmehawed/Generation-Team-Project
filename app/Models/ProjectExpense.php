<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    protected $fillable =
    ['team_id', 'item_name', 'quantity', 'price', 'receipt_image', 'buyer_name'];

    public function contributions()
    {
        // Adjust class name if your model is named differently (e.g., FundContribution)
        return $this->hasMany(\App\Models\ExpenseContribution::class);
    }
    // علاقة التيم
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
