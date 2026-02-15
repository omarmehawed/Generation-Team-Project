<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Project;
use App\Models\Team;
use App\Notifications\BatuNotification;

class StaffSubjectController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        // ✅ التصحيح: نجيب الترم من الداتابيز (زي الدكتور) مش من ملف .env
        $currentTerm = \App\Models\Setting::where('key', 'current_term')->value('value') ?? 1;

        $courses = $user->courses()
            ->where('term', $currentTerm) // عشان نتأكد إننا بنعرض مواد الترم الحالي بس
            ->with('projects')
            ->get();



        return view('staff.subjects.index', compact('courses'));
    }

    // دالة الإدارة اللي هنعملها الخطوة الجاية
    public function manage($id)
    {
        $course = \App\Models\Course::with('projects.teams')->findOrFail($id);

        // هنجيب المشروع الأول للكورس ده (بافتراض الكورس له مشروع واحد حالياً)
        $project = $course->projects->first();

        // لو مفيش مشروع، ممكن نرجع بـ error أو نوديه صفحة إنشاء مشروع
        if (!$project) {
            return back()->with('error', 'No project created for this course yet.');
        }

        // نجيب التيمات المسجلة في المشروع ده
        $teams = $project->teams()->with('members.user')->paginate(10);

        return view('staff.subjects.manage_subject_project', compact('course', 'project', 'teams'));
    }


    public function updateSettings(Request $request, $id)
    {
        $request->validate([
            'deadline' => 'required|date',
            'leave_team_deadline' => 'nullable|date', // الحقل الجديد
        ]);

        $project = Project::findOrFail($id);

        $project->update([
            'deadline' => $request->deadline,
            'leave_team_deadline' => $request->leave_team_deadline, // الحفظ
        ]);


        //  النوتيفيكيشن: نبلغ كل الليدرز بتوع التيمات في المشروع ده
        $teams = Team::where('project_id', $project->id)->with('leader')->get();

        foreach ($teams as $team) {
            if ($team->leader) {
                $team->leader->notify(new BatuNotification([
                    'title'   => 'Deadline Updated ⏳',
                    'body'    => "The deadline for {$project->name} has been changed to " . \Carbon\Carbon::parse($request->deadline)->format('d M Y'),
                    'icon'    => 'fas fa-clock',
                    'color'   => 'text-orange-500',
                    'url'     => route('projects.show', $project->id), // لينك صفحة المشروع عند الطالب
                    'type'    => 'alert'
                ]));
            }
        }

        return back()->with('success', 'Project settings updated successfully!');
    }


    // عرض تفاصيل التيم (تاسكات + أعضاء)
    public function viewTeam($teamId)
    {
        $team = Team::with(['project', 'members.user', 'tasks.user'])->findOrFail($teamId);
        return view('staff.subjects.view_team', compact('team'));
    }

    // حفظ الدرجات الفردية
    public function saveIndividualGrades(Request $request, $teamId)
    {
        $request->validate([
            'grades' => 'array',
            'grades.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $team = Team::findOrFail($teamId);

        // اللوب ده بيلف على مصفوفة الدرجات ويحفظها في الداتابيز
        // grades[user_id] = score
        foreach ($request->grades as $userId => $score) {
            // تحديث الجدول الوسيط team_members
            \DB::table('team_members')
                ->where('team_id', $teamId)
                ->where('user_id', $userId)
                ->update(['individual_score' => $score]);
        }

        return back()->with('success', 'Individual grades saved successfully!');
    }
    // تحديث دالة الحفظ لتشمل درجة المشروع + الدرجات الفردية
    public function saveAllGrades(Request $request, $teamId)
    {
        $request->validate([
            // التحقق من درجات المشروع
            'project_score' => 'nullable|numeric|min:0',
            'project_max_score' => 'required|numeric|min:1',

            // التحقق من درجات الطلاب (مصفوفات)
            'individual_grades' => 'array',
            'individual_grades.*' => 'nullable|numeric|min:0',

            'individual_max_scores' => 'array',
            'individual_max_scores.*' => 'required|numeric|min:1',
        ]);

        // 1. نتأكد إن درجة المشروع مش أكبر من العظمى بتاعته
        if ($request->project_score > $request->project_max_score) {
            return back()->with('error', 'Project Score cannot be higher than Max Score!');
        }

        $team = Team::findOrFail($teamId);

        // حفظ درجة المشروع
        $team->update([
            'project_score' => $request->project_score,
            'project_max_score' => $request->project_max_score
        ]);

        // 2. حفظ درجات الطلاب
        if ($request->has('individual_grades')) {
            foreach ($request->individual_grades as $userId => $score) {

                // نجيب الدرجة العظمى الخاصة بالطالب ده من الريكويست
                $maxScore = $request->individual_max_scores[$userId] ?? 100;

                // نتأكد إن درجة الطالب مش أكبر من عظمته هو
                if ($score > $maxScore) {
                    $user = \App\Models\User::find($userId);
                    return back()->with('error', "Score for {$user->name} cannot be higher than their Max Score!");
                }

                \DB::table('team_members')
                    ->where('team_id', $teamId)
                    ->where('user_id', $userId)
                    ->update([
                        'individual_score' => $score,
                        'individual_max_score' => $maxScore // 👈 حفظ العظمى الجديدة
                    ]);
            }
        }

        //  النوتيفيكيشن: نبلغ أعضاء التيم إن الدرجات نزلت
        foreach ($team->members as $member) {
            $member->user->notify(new BatuNotification([
                'title'   => 'Grades Released 🎓',
                'body'    => "Grades for {$team->project->name} have been updated. Check them now!",
                'icon'    => 'fas fa-graduation-cap',
                'color'   => 'text-blue-600',
                'url'     => route('projects.show', $team->project_id), // يروح يشوف درجته
                'type'    => 'info'
            ]));
        }

        return back()->with('success', 'All grades saved successfully!');
    }
    // ضيف دالة الاكسبورت دي في آخر الملف
    public function exportTeams(Request $request)
    {
        $teamIds = json_decode($request->teams);

        if (empty($teamIds)) {
            return back()->with('error', 'No teams selected.');
        }

        // 1. استقبال الاسم
        $customName = $request->input('file_name');

        // 2. نجيب اسم الكورس عشان نحطه في الهيدر
        // بنجيب أول تيم ومنه المشروع ومنه الكورس
        $firstTeam = \App\Models\Team::with('project.course')->find($teamIds[0]);
        $courseName = $firstTeam ? ($firstTeam->project->course->name ?? 'Subject') : 'Subject';

        $headerTitle = $courseName; // ده اللي هيتكتب جوه الملف

        // 3. تجهيز اسم الملف
        if ($customName) {
            $cleanName = \Illuminate\Support\Str::slug(str_replace('.xlsx', '', $customName), '_');
            $fileName = $cleanName . '.xlsx';
        } else {
            $fileName = 'Subject_Teams_' . date('Y-m-d') . '.xlsx';
        }

        // 4. نبعت البيانات والعنوان للكلاس الجديد
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SubjectTeamsExport($teamIds, $headerTitle), $fileName);
    }
}
