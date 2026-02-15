<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Carbon\Carbon;

class GraduationProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::updateOrCreate(
            ['type' => 'graduation'],
            [
                'title' => 'Graduation Project 2026',
                'description' => 'The main graduation project for senior students.',
                'type' => 'graduation',
                'is_active' => true,
                'deadline' => Carbon::now()->addMonths(5),
                'max_members' => 6,
                'course_id' => null,

                // 👇👇 شيلنا السطر ده عشان ميعملش مشاكل
                // 'department' => 'general' 
            ]
        );
    }
}
