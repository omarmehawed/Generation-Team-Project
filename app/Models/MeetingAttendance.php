<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAttendance extends Model
{
    // تأكد من اسم الجدول الصحيح
    protected $table = 'meeting_attendances';

    // الأعمدة المسموح بتعبئتها (يجب أن تطابق السكيما)
    protected $fillable = ['meeting_id', 'user_id', 'is_present'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
