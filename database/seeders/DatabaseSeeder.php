<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. حساب الدكتور (Big Boss)
        User::create([
            'name' => 'Omar Mehawed',
            'email' => '2420823@batechu.com',
            'password' => bcrypt('123456789'), // الباسورد للتجربة
            'role' => 'member', // أهم حتة
            'academic_year' => '2',
            'department' => 'general',
        ]);
        // 1. حساب الدكتور (Big Boss)
        User::create([
            'name' => 'Dr. Osama El-Nahhas',
            'email' => 'osama@batechu.com',
            'password' => bcrypt('123456789'), // الباسورد للتجربة
            'role' => 'admin', // أهم حتة
            'academic_year' => '0',
            'department' => 'general',
        ]);

        // 2. حساب المعيد (TA)
        User::create([
            'name' => 'Eng. Ahmed Sultan',
            'email' => 'sultan@batechu.com',
            'password' => bcrypt('123456789'),
            'role' => 'ta',
            'academic_year' => '0',
            'department' => 'general',
        ]);

        User::create([
            'name' => 'Fares Elsayed',
            'email' => '2420873@batechu.com',
            'password' => bcrypt('123456789'), // الباسورد للتجربة
            'role' => 'member', // أهم حتة
            'academic_year' => '2',
            'department' => 'general',
        ]);

        User::create([
            'name' => 'Omar Mehawed',
            'email' => 'omar_mehawed@batechu.com',
            'university_email' => null,
            'academic_year' => '0', // الأدمن ملوش سنة دراسية
            'password' => bcrypt('123456789'), // الباسورد للتجربة
            'role' => 'admin', // أهم حتة
            'email_verified_at' => now(),
            'department' => 'general',
            // الصلاحيات (لارافل هيحولها JSON لوحده عشان الـ casts في الموديل)
            'permissions' => [
                "view_proposals",
                "manage_teams",
                "manage_subjects",
                "view_defense"
            ],
        ]);


        $this->call([
            CourseSeeder::class,
            GraduationProjectSeeder::class,
            AssignCoursesToStaffSeeder::class,
        ]);

        // ملاحظة: لو عندك كود قديم هنا سيبه عادي أو امسحه لو عايز تنضف
    }
}
