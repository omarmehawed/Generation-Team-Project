<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
   use HasFactory;

   // 🔥 دي أهم حتة في المشروع كله دلوقتي
   // لازم 'status' تكون مكتوبة هنا عشان تتسجل في الداتا بيز
   protected $fillable = [
      'meeting_id',
      'user_id',
      'status'
   ];

   // العلاقات بتاعتك زي ما هي
   public function meeting()
   {
      return $this->belongsTo(Meeting::class);
   }

   public function student()
   {
      return $this->belongsTo(User::class, 'student_id');
   }
}
