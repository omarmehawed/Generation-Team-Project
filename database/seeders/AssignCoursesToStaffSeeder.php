<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;

class AssignCoursesToStaffSeeder extends Seeder
{
    public function run()
    {
        // 1. هات كل آيديهات الكورسات الموجودة في النظام
        $allCourseIds = Course::pluck('id');

        // 2. هات المستخدمين اللي عايز تربطهم (ID 2 و ID 3)
        // ممكن تزود أي ID تاني هنا في المصفوفة [2, 3, 5, ...]
        $staffMembers = User::whereIn('id', [2, 3])->get();

        // 3. اللف عليهم وربطهم بالكورسات
        foreach ($staffMembers as $user) {
            // syncWithoutDetaching: بتضيف العلاقات الجديدة بس وبتحافظ على القديم
            // يعني لو الدكتور مربوط بمادة بالفعل، مش هيعمل حاجة، لو مش مربوط هيربطه
            $user->courses()->syncWithoutDetaching($allCourseIds);
        }
    }
}
