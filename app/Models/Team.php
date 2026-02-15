<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProjectExpense;
use App\Models\ProjectGallery;

class Team extends Model
{
    use HasFactory;

    protected $guarded = []; // السماح بكل البيانات

    // علاقة التيم بالمشروع
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // علاقة التيم بالأعضاء (دي اللي كانت ناقصة ومسببة المشكلة)
    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }

    // علاقة التيم بالقائد (Leader)
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }
    // علاقة التيم بالمهام
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // علاقة المصاريف (التيم عنده مصاريف كتير)
    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class);
    }

    // علاقة الأرشيف (التيم عنده صور وفيديوهات كتير)
    public function gallery()
    {
        return $this->hasMany(ProjectGallery::class);
    }


    //protected gured

    protected $fillable = [
        'project_id',
        'name',
        'code',
        'leader_id',
        'status',
        'logo',
        'submission_path',
        'submission_link',
        'submission_comment',

        'proposal_title',
        'proposal_description',
        'proposal_file',
        'proposal_status',
        'project_phase',
        'ta_id',

        'defense_date',
        'defense_location',
        'project_phase',
        'project_score',      // الدرجة اللي جابها
        'project_max_score',  // الدرجة النهائية (من كام)
    ];

    // علاقة التقارير الأسبوعية
    public function reports()
    {
        // تأكد إنك عامل import لموديل WeeklyReport فوق أو اكتب المسار كامل
        return $this->hasMany(\App\Models\WeeklyReport::class);
    }
    // علاقة شكاوى الأعضاء
    public function memberReports()
    {
        return $this->hasMany(\App\Models\MemberReport::class);
    }

    // علاقة المعيد (TA) بجدول الداتا بيز لو في معيد يبقي فاينال بروجكت لو مفيش يبقي سبجكت
    public function ta()
    {
        return $this->belongsTo(User::class, 'ta_id');
    }


    // علاقة الاجتماعات (التيم عنده اجتماعات كتير)
    public function meetings()
    {
        // تأكد إن عندك موديل اسمه Meeting
        // لو الموديل مش موجود، شيل السطر ده من الكنترولر مؤقتاً
        return $this->hasMany(\App\Models\Meeting::class);
    }
}
