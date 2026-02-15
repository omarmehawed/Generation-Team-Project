<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    //  الحل هنا: ضفتلك كل الحقول الناقصة
    protected $fillable = [
        'team_id',      // <--- كان ناقص
        'title',        // <--- كان ناقص
        'description',  // <--- كان ناقص
        'deadline',     // <--- كان ناقص
        'assigned_by',  // <--- كان ناقص
        'user_id',
        'submission_type',
        'submission_value', // (اللينك)
        'submission_file',
        'submission_comment',
        'status',
        'submitted_at',
        'grade',
        'feedback',
        'graded_at',
        'graded_by'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'deadline' => 'datetime', // <--- يفضل تضيف دي كمان
        'grade' => 'decimal:2',
    ];

    // علاقة بالمهمة
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // علاقة بالمستخدم الذي قام بالتسليم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة بالمستخدم الذي قام بالتصحيح
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scope للطلبات المعلقة
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope للطلبات المصححة
    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    // Scope للطلبات المتأخرة
    public function scopeLate($query)
    {
        return $query->where('submitted_at', '>', $this->task->deadline ?? null);
    }

    // الحصول على مسار الملف
    public function getFilePathAttribute()
    {
        if ($this->submission_file) {
            return storage_path('app/task_submissions/' . $this->submission_file);
        }
        return null;
    }

    // الحصول على رابط تحميل الملف
    public function getFileUrlAttribute()
    {
        if ($this->submission_file) {
            return route('tasks.download', $this->id);
        }
        return null;
    }

    // الحصول على اسم الملف
    public function getFileNameAttribute()
    {
        if ($this->submission_file) {
            return basename($this->submission_file);
        }
        return null;
    }

    // الحصول على حجم الملف
    public function getFileSizeAttribute()
    {
        if ($this->submission_file && file_exists($this->file_path)) {
            return filesize($this->file_path);
        }
        return null;
    }

    // الحصول على صيغة الملف
    public function getFileFormatAttribute()
    {
        if ($this->submission_file) {
            return pathinfo($this->submission_file, PATHINFO_EXTENSION);
        }
        return null;
    }

    // التحقق إذا كان التقديم متأخراً
    public function getIsLateAttribute()
    {
        if (!$this->task || !$this->submitted_at) {
            return false;
        }

        return $this->submitted_at->gt($this->task->deadline);
    }

    // الحصول على حالة التقديم
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'submitted' => 'تم التقديم',
            'graded' => 'تم التصحيح',
            'returned' => 'تم الإرجاع',
            'rejected' => 'مرفوض'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // الحصول على لون الحالة
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'submitted' => 'info',
            'graded' => 'success',
            'returned' => 'secondary',
            'rejected' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // الحصول على الرمز البريدي للإرسال
    public function getZipCodeAttribute()
    {
        return 'TASK' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // حذف الملف المرفوع عند حذف السجل
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($taskSubmission) {
            if ($taskSubmission->submission_file && file_exists($taskSubmission->file_path)) {
                unlink($taskSubmission->file_path);
            }
        });
    }


    // هل التاسك ده متأخر؟ (للتاسكات اللي لسه متسلمتش)
    public function getIsOverdueAttribute()
    {
        // لو لسه "قيد الانتظار" والوقت الحالي عدى الديدلاين
        return $this->status == 'pending' && $this->deadline && now()->gt($this->deadline);
    }

    // هل تم التسليم متأخر؟ (للتاسكات اللي اتسلمت خلاص)
    public function getIsSubmittedLateAttribute()
    {
        // لو اتسلمت، ووقت التسليم كان بعد الديدلاين
        return $this->submitted_at && $this->deadline && $this->submitted_at->gt($this->deadline);
    }
}
