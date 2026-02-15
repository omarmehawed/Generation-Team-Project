<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. تنظيف الجداول القديمة (عشان نبدأ على نظافة وميحصلش تكرار)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('course_user')->truncate();
        DB::table('courses')->truncate();
        // لو عايز تمسح المشاريع القديمة كمان:
        // DB::table('projects')->truncate(); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. تعريف خريطة المواد الأكاديمية (الصح)
        $courses = [
            // ==========================
            // 🟢 الفرقة الأولى (سنة 1)
            // ==========================
            [
                'name' => 'Cyber Security',
                'code' => 'IT101',
                'year' => 1,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-shield-alt',
                'color' => 'red'
            ],
            // 1. Python (الترم الأول)
            [
                'name' => 'Python Programming',
                'code' => 'CS101',
                'year' => 1,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fab fa-python', // أيقونة بايثون الرسمية
                'color' => 'purple' // لون بايثون المشهور
            ],

            // Cyber Security 2 (Advanced)
            [
                'name' => 'Cyber Security 2',
                'code' => 'IT205', // كود جديد
                'year' => 1,       // خليناها سنة تانية
                'term' => 2,       // ترم تاني (عشان الترم الأول فيه C++ و DB)
                'dept' => 'general',
                'icon' => 'fas fa-user-secret', // أيقونة مختلفة (Spy/Hacker)
                'color' => 'red'
            ],
            // 2. C Programming (الترم الثاني)
            [
                'name' => 'C Programming',
                'code' => 'CS102',
                'year' => 1,
                'term' => 2,
                'dept' => 'general',
                'icon' => 'fas fa-terminal', // أيقونة التيرمينال عشان الـ C
                'color' => 'gray'
            ],

            // Microsoft Office 
            [
                'name' => 'Microsoft Office',
                'code' => 'IT100',   // كود مميز للمواد التمهيدية
                'year' => 1,         // سنة أولى
                'term' => 2,         // ترم تاني
                'dept' => 'general',
                'icon' => 'fab fa-microsoft', // أيقونة مايكروسوفت
                'color' => 'blue'    // اللون الأزرق بتاع الأوفيس
            ],

            // ==========================
            // 🟠 الفرقة الثانية (سنة 2)
            // ==========================
            [
                'name' => 'Web Programming 1',
                'code' => 'IT201',
                'year' => 2,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-code',
                'color' => 'orange'
            ],

            // Linux Essentials (أساسيات لينكس)
            [
                'name' => 'Linux Essentials',
                'code' => 'IT204',   // كود مقترح
                'year' => 2,         // سنة تانية
                'term' => 1,         // ترم أول
                'dept' => 'general',
                'icon' => 'fab fa-linux', // أيقونة البطريق
                'color' => 'slate'   // لون رصاصي غامق يليق بالـ Terminal
            ],

            // 3. C++ Programming (الترم الأول)
            [
                'name' => 'C++ Programming',
                'code' => 'CS201',
                'year' => 2,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-code', // أيقونة الكود
                'color' => 'blue'
            ],

            // 4. Introduction to Database (الترم الأول)
            [
                'name' => 'Introduction to Database',
                'code' => 'IS201',
                'year' => 2,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-database', // أيقونة قواعد البيانات
                'color' => 'indigo'
            ],

            [
                'name' => 'Web Programming 2',
                'code' => 'IT202',
                'year' => 2,
                'term' => 2,
                'dept' => 'general',
                'icon' => 'fas fa-laptop-code',
                'color' => 'orange'
            ],
            // Database Programming (تكملة للـ Database)
            [
                'name' => 'Database Programming',
                'code' => 'IS202',   // كود مكمل لـ IS201 (Intro to DB)
                'year' => 2,         // سنة تانية
                'term' => 2,         // ترم تاني
                'dept' => 'general',
                'icon' => 'fas fa-file-code', // أيقونة ملف كود (SQL Script)
                'color' => 'teal'    // لون مميز (Teal)
            ],
            [
                'name' => 'Java 1',
                'code' => 'IT203',
                'year' => 2,
                'term' => 2,
                'dept' => 'general',
                'icon' => 'fab fa-java',
                'color' => '(235, 35, 123)'
            ],

            // ==========================
            // 🔵 الفرقة الثالثة (سنة 3)
            // ==========================
            // مواد مشتركة (ترم أول)
            [
                'name' => 'Java Advanced',
                'code' => 'IT301',
                'year' => 3,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fab fa-java',
                'color' => 'blue'
            ],
            [
                'name' => 'Microprocessor',
                'code' => 'IT302',
                'year' => 3,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-microchip',
                'color' => 'gray'
            ],
            [
                'name' => 'Computer Architecture',
                'code' => 'IT303',
                'year' => 3,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-server',
                'color' => 'indigo'
            ],
            // ==========================
            // 🔵 مواد تخصص Software (سنة 3)
            // ==========================

            // C Advanced (سنة 3 - ترم أول)
            [
                'name' => 'C Advanced',
                'code' => 'SW302',   // كود جديد في تخصص السوفتوير
                'year' => 3,
                'term' => 1,
                'dept' => 'software',
                'icon' => 'fas fa-terminal', // C لغة قوية في التعامل مع النظام (Terminal)
                'color' => 'gray'    // لون كلاسيكي
            ],


            // تخصص Software (ترم تاني)
            [
                'name' => 'Mobile Application (Part 1)',
                'code' => 'SW301',
                'year' => 3,
                'term' => 2,
                'dept' => 'software',
                'icon' => 'fas fa-mobile-alt',
                'color' => 'pink'
            ],
            // C++ Advanced (سنة 3 - ترم تاني)
            [
                'name' => 'C++ Advanced',
                'code' => 'SW303',   // كود تخصصي
                'year' => 3,
                'term' => 2,
                'dept' => 'software',
                'icon' => 'fas fa-code-branch', // تعبير عن الـ OOP والـ Pointers
                'color' => 'indigo'  // لون يعبر عن العمق والاحترافية
            ],
            // تخصص Network (ترم تاني)
            [
                'name' => 'CCNA (Part 1)',
                'code' => 'NW301',
                'year' => 3,
                'term' => 2,
                'dept' => 'network',
                'icon' => 'fas fa-network-wired',
                'color' => 'cyan'
            ],

            // ==========================
            // 🟣 الفرقة الرابعة (سنة 4)
            // ==========================
            // مواد مشتركة (AI ترم أول + ML ترم تاني)
            [
                'name' => 'Artificial Intelligence',
                'code' => 'IT401',
                'year' => 4,
                'term' => 1,
                'dept' => 'general',
                'icon' => 'fas fa-brain',
                'color' => 'purple'
            ],
            [
                'name' => 'Machine Learning',
                'code' => 'IT402',
                'year' => 4,
                'term' => 2,
                'dept' => 'general',
                'icon' => 'fas fa-robot',
                'color' => 'green'
            ],
            // تخصص Software (ترم أول - تكملة)
            [
                'name' => 'Mobile Application (Part 2)',
                'code' => 'SW401',
                'year' => 4,
                'term' => 1,
                'dept' => 'software',
                'icon' => 'fas fa-mobile',
                'color' => 'pink'
            ],
            // تخصص Network (ترم أول - تكملة)
            [
                'name' => 'CCNA (Part 2)',
                'code' => 'NW401',
                'year' => 4,
                'term' => 1,
                'dept' => 'network',
                'icon' => 'fas fa-wifi',
                'color' => 'cyan'
            ],
        ];

        // 3. إنشاء المواد والمشاريع في الداتابيز
        foreach ($courses as $data) {
            // إنشاء أو تحديث الكورس
            $course = Course::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'year_level' => $data['year'],
                    'term' => $data['term'],
                    'department' => $data['dept'],
                    'icon_class' => $data['icon'], // تأكد إن العمود في الداتابيز اسمه كدة
                    'color' => $data['color'],
                ]
            );

            // إنشاء مشروع افتراضي للمادة (عشان تكون جاهزة للدكتور)
            Project::firstOrCreate(
                ['course_id' => $course->id],
                [
                    'title' => $data['name'] . ' Project',
                    'description' => 'Official course project. Please submit your work before the deadline.',
                    'deadline' => Carbon::now()->addMonth(), // ديدلاين بعد شهر
                    'max_members' => 5,
                    'max_score' => 100,
                    'is_active' => true,
                ]
            );
        }

        // 4. توزيع الطلاب الموجودين على الفرق والتخصصات
        $this->distributeStudents();
    }

    /**
     * دالة مساعدة لتوزيع الطلاب وتسجيلهم في المواد
     */
    private function distributeStudents()
    {
        // هات كل الطلاب
        $students = User::where('role', 'member')->get();

        if ($students->isEmpty()) {
            $this->command->info('No students found to distribute.');
            return;
        }

        foreach ($students as $index => $student) {
            // توزيع عشوائي منتظم: 1, 2, 3, 4
            $year = ($index % 4) + 1;

            // تحديد التخصص (فقط لسنة 3 و 4)
            $dept = 'general';
            if ($year >= 3) {
                // نصهم Software ونصهم Network
                $dept = ($index % 2 == 0) ? 'software' : 'network';
            }

            // تحديث بيانات الطالب
            $student->update([
                'academic_year' => $year,
                'department' => $dept
            ]);

            // تسجيل الطالب في المواد المناسبة لسنة وتخصصه
            $this->enrollInCourses($student);
        }
    }

    /**
     * تسجيل طالب واحد في المواد الخاصة به
     */
    private function enrollInCourses($student)
    {
        // اللوجيك: هات مواد نفس السنة، بشرط تكون (عامة) أو (نفس قسم الطالب)
        $courses = Course::where('year_level', $student->academic_year)
            ->where(function ($query) use ($student) {
                $query->where('department', 'general')
                    ->orWhere('department', $student->department);
            })
            ->get();

        // ربط الطالب بالمواد (Sync عشان ميكررش)
        $student->courses()->sync($courses->pluck('id'));
    }
}
