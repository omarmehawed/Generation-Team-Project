<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseContribution extends Model
{
    use HasFactory;

    protected $guarded = [];

    // العلاقة مع المصروف (Fund)
    public function expense()
    {
        return $this->belongsTo(ProjectExpense::class, 'project_expense_id'); // تأكد من اسم العمود في الداتا بيز
    }

    // العلاقة مع الطالب (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}