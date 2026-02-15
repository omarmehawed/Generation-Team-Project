<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundContribution extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة (بما فيها طريقة الدفع والصورة اللي ضفناهم مؤخراً)
    protected $fillable = [
        'fund_id',
        'user_id',
        'status',
        'paid_at',
        'payment_method',
        'payment_proof',
        'transaction_date',
        'transaction_time',
        'from_number',
        'notes',
        'rejection_reason'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // 1. علاقة العضو (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. العلاقة اللي كانت ناقصة (Fund)
    // دي عشان نعرف المساهمة دي تابعة لأي طلب تمويل
    public function fund()
    {
        return $this->belongsTo(ProjectFund::class, 'fund_id');
    }
}
