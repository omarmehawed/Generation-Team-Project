<?php

use App\Models\User;
use App\Models\WeeklyEvaluation;
use App\Models\EvaluationItem;
use Illuminate\Support\Facades\Route;

Route::get('/test-pdf-gen/{id?}', function ($id = null) {
    // 1. Create or Find User
    $user = User::first() ?? User::factory()->create();
    
    // 2. Create Evaluation
    $eval = WeeklyEvaluation::firstOrCreate(
        ['student_id' => $user->id, 'week_number' => 999],
        ['commitment_level' => 5, 'satisfaction_level' => 5, 'general_notes' => 'Test PDF Generation (Table Layout)', 'created_by' => $user->id]
    );

    // 3. Create Items if not exists
    if ($eval->items()->count() == 0) {
        $eval->items()->create(['type' => 'task', 'title' => 'Design PDF', 'rating' => 'excellent', 'order' => 0]);
        $eval->items()->create(['type' => 'task', 'title' => 'Fix Bugs', 'rating' => 'good', 'order' => 1]);
        $eval->items()->create(['type' => 'quiz', 'title' => 'Laravel', 'mark' => 5, 'order' => 0]);
        $eval->items()->create(['type' => 'meeting', 'title' => 'Sprint Planning', 'rating' => 'attended', 'order' => 0]);
        $eval->items()->create(['type' => 'workshop', 'title' => 'Vue.js', 'rating' => 'attended', 'order' => 0]);
        $eval->items()->create(['type' => 'activity', 'title' => 'Team Building', 'rating' => 'good', 'order' => 0]);
    }

    // 4. Trigger PDF Generation Controller Logic (Simulated)
    $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.weekly_evaluation', ['evaluation' => $eval]);
    return $pdf->stream('test.pdf');
});
