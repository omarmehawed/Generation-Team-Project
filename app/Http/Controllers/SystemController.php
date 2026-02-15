<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    // 1. تغيير الترم (Switch Term)
    public function changeTerm(Request $request)
    {
        set_time_limit(3000); // خلي الوقت المسموح 300 ثانية (5 دقايق) بدل 30 ثانية
        ini_set('memory_limit', '512M'); // احتياطي عشان الذاكرة متضربش
        $newTerm = $request->term; // 1 or 2

        // تحديث الإعداد في الداتابيز
        Setting::updateOrCreate(['key' => 'current_term'], ['value' => $newTerm]);

        //  السحر: تحديث مواد كل الطلبة بناءً على الترم الجديد
        $this->syncAllStudentsCourses($newTerm);

        return back()->with('success', "System switched to Term $newTerm successfully! Courses updated.");
    }

    // 2. ترحيل السنة (Promote Students)
    public function promoteStudents()
    {
        // نستخدم Transaction عشان لو حصل غلطة نرجع في كلامنا
        DB::transaction(function () {
            // أ. ترحيل كل الطلبة للسنة التالية
            // (سنة 1 تبقى 2، وهكذا.. وسنة 4 تفضل 4 أو تتخرج حسب اللوجيك بتاعك)
            User::where('role', 'student')->where('academic_year', '<', 4)->increment('academic_year');

            // ب. نرجع النظام للترم الأول
            Setting::updateOrCreate(['key' => 'current_term'], ['value' => '1']);

            // ج. تحديث المواد بناءً على السنة الجديدة والترم الأول
            $this->syncAllStudentsCourses(1);
        });

        return back()->with('success', "Students promoted to next year successfully!");
    }

    // دالة مساعدة: بتلف على الطلبة وتوزع المواد
    private function syncAllStudentsCourses($term)
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // هات مواد سنة الطالب + الترم المحدد + القسم بتاعه
            $courses = Course::where('year_level', $student->academic_year)
                ->where('term', $term)
                ->where(function ($q) use ($student) {
                    $q->where('department', 'general')
                        ->orWhere('department', $student->department);
                })
                ->get();

            // سجل المواد دي في جدول course_user
            // (sync) بتمسح القديم وتحط الجديد، وده اللي إحنا عايزينه عشان مواد الترم اللي فات تختفي ويظهر الجديد
            // لو عايز تحتفظ بالقديم استخدم syncWithoutDetaching
            $student->courses()->sync($courses->pluck('id'));
        }
    }
}
