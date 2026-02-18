<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
// 1. أهم خطوة: استدعاء كلاسات الـ Log هنا فوق
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()

            ->dontLogIfAttributesChangedOnly(['deleted_by_id', 'updated_at']);
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'university_email',
        'role',
        'academic_year',
        'department',
        'permissions',
        'created_by_id',
        'deleted_by_id',
        'team_id', // [NEW] Link to Team
        'phone_number',
        'whatsapp_number',
        'national_id',
        'date_of_birth',
        'address', // Keep existing address if used elsewhere, or map home_address to it
        'home_address',
        'is_dorm',
        'profile_photo_path',
        'wallet_balance', // [NEW] Wallet System
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    // ✅ دالة سحرية عشان نفحص الصلاحية في السايد بار
    public function hasPermission($permission)
    {

        // حتى لو هو Admin، لو مش معاه الصلاحية دي مكتوبة، مياخدهاش.
        if ($permission === 'backup_db') {
            return !empty($this->permissions) && in_array($permission, $this->permissions);
        }

        // 2. باقي الصلاحيات العادية (Admin = Leader) ✅
        if ($this->role === 'admin') {
            return true;
        }

        // 3. الفحص العادي لباقي الموظفين (Members)
        if (empty($this->permissions)) {
            return false;
        }
        return in_array($permission, $this->permissions);
    }

    // العلاقات القديمة زي ما هي
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function weeklyEvaluations()
    {
        return $this->hasMany(WeeklyEvaluation::class, 'student_id');
    }

    public function joinRequest()
    {
        return $this->hasOne(JoinRequest::class, 'user_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function approvedRequests()
    {
        return $this->hasMany(JoinRequest::class, 'approved_by');
    }

    // ✅ علاقة مين اللي مسح اليوزر
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    protected static function booted()
    {
        // 1️⃣ مراقبة الإنشاء (Create)
        static::created(function ($user) {
            if (Auth::check()) { // لو فيه أدمن مسجل دخول هو اللي عمل كدة
                ActivityLog::create([
                    'causer_id' => Auth::id(),
                    'subject_id' => $user->id,
                    'action' => 'Created',
                    'changes' => ['attributes' => $user->getAttributes()], // سجل البيانات الجديدة
                ]);
            }
        });

        // 2️⃣ مراقبة التعديل (Update)
        static::updated(function ($user) {
            if (Auth::check()) {
                // هات التغييرات بس (القديم والجديد)
                $changes = [
                    'before' => array_intersect_key($user->getOriginal(), $user->getDirty()),
                    'after' => $user->getDirty(),
                ];

                ActivityLog::create([
                    'causer_id' => Auth::id(),
                    'subject_id' => $user->id,
                    'action' => 'Updated',
                    'changes' => $changes,
                ]);
            }
        });

        // 3️⃣ مراقبة الحذف (Delete)
        static::deleted(function ($user) {
            if (Auth::check()) {
                ActivityLog::create([
                    'causer_id' => Auth::id(),
                    'subject_id' => $user->id,
                    'action' => 'Deleted',
                    'changes' => null,
                ]);
            }
        });
    }
}
