<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // السطر ده هو اللي بيحل المشكلة (بيسمح بإدخال أي بيانات)
    protected $guarded = [];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * العلاقة بين المادة والمستخدمين (دكاترة وطلاب)
     * علاقة Many-to-Many
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }
}
