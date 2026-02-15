<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Team;
use App\Notifications\BatuNotification;

class ProjectController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. بنجيب رقم الترم الحالي من ملف الإعدادات .env
        // لو مش موجود، بنفترض إنه الترم الأول (1) افتراضياً
        $currentTerm = \App\Models\Setting::where('key', 'current_term')->value('value') ?? 1;

        // 2. بنجيب المواد بتاعة الطالب، بس بنعمل "فلتر" على الترم
        $courses = $user->courses()
            ->where('term', $currentTerm)
            ->with('projects') // وبنجيب المشاريع اللي جواها
            ->get();

        return view('projects.index', compact('courses'));
    }


    public function show(Project $project)
    {
        $userId = Auth::id();

        // Find user's team for this project
        $myTeam = Team::where('project_id', $project->id)
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->first();

        // Ensure $myRole ALWAYS exists
        $myRole = null;

        if ($myTeam) {
            $myRole = $myTeam->members()
                ->where('user_id', $userId)
                ->value('role');  // "leader" or "member"
        }

        return view('projects.show', [
            'project' => $project,
            'myTeam' => $myTeam,
            'myRole' => $myRole,
        ]);
    }
}
