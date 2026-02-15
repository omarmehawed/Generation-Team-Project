<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // السطر السحري لفك الحماية
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function leader()
    {
        // بنقوله الليدر هو المستخدم اللي الـ ID بتاعه مكتوب في عمود student_id
        return $this->belongsTo(User::class, 'student_id');
    }

    // 2. علاقة التيم (الفريق)

    // علاقة المشروع بالتيمات: المشروع الواحد فيه تيمات كتير
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    // 3. علاقة الدكتور المشرف (للمستقبل)
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'ta_id');
    }
    // علاقة الميزانية (Funds)
    public function funds()
    {
        return $this->hasMany(\App\Models\ProjectFund::class);
    }
}
