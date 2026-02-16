<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\WeeklyReport;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\MeetingAttendance;    // وضيف دي عشان تحل التحذير التاني
use App\Notifications\BatuNotification;
use App\Exports\TeamsExport;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends Controller
{
    // 1. دالة الداشبورد (الإحصائيات فقط)
    public function index()
    {
        $user = Auth::user();


        $user = Auth::user();
        $uid = $user->id;


        // 1️⃣ تعريف الشروط (Conditions) عشان ندمج المصدرين

        // شرط مشاريع التخرج (زي كودك القديم بالظبط)
        $gradCondition = function ($q) use ($uid) {
            $q->where('ta_id', $uid);
            // لو عايز تضيف doctor_id مستقبلاً ممكن تزود ->orWhere('doctor_id', $uid) هنا
        };

        // شرط مشاريع المواد (عن طريق الكورسات)
        $subjectCondition = function ($q) use ($uid) {
            $q->whereHas('project.course.users', function ($subQ) use ($uid) {
                $subQ->where('users.id', $uid);
            });
        };

        // دالة بتجمع الشرطين (أي تيم يخصك سواء تخرج أو مادة)
        $allMyTeams = function ($q) use ($gradCondition, $subjectCondition) {
            $q->where(function ($query) use ($gradCondition, $subjectCondition) {
                $query->where($gradCondition)
                    ->orWhere($subjectCondition);
            });
        };


        // 2️⃣ الإحصائيات (Stats) - تجميع دقيق

        // أ) إجمالي التيمات (تخرج + مواد)
        $totalTeams = Team::where($allMyTeams)->count();

        // ب) المقترحات المعلقة (غالباً تخرج)
        $pendingProposals = Team::where($gradCondition)
            ->where(function ($q) {
                $q->where('proposal_status', 'pending')
                    ->orWhere('status', 'pending'); // احتياطي لو الاسم مختلف
            })->count();

        // ج) المشاريع الشغالة (تخرج + مواد)
        $activeProjects = Team::where($allMyTeams)
            ->where('project_phase', '!=', 'completed')
            ->where('project_phase', '!=', 'finished')
            ->count();

        // د) المشاريع المكتملة (تخرج + مواد)
        $completedProjects = Team::where($allMyTeams)
            ->whereIn('project_phase', ['completed', 'finished'])
            ->count();

        $stats = [
            'total_teams' => $totalTeams,
            'pending_proposals' => $pendingProposals,
            'active_projects' => $activeProjects,
            'completed_projects' => $completedProjects
        ];


        // 3️⃣ الجداول والقوائم (Lists)

        // 1. المناقشات القادمة (خاصة بمشاريع التخرج)
        $upcoming_defenses = Team::where($gradCondition)
            ->whereNotNull('defense_date')
            ->where('defense_date', '>=', now())
            ->orderBy('defense_date', 'asc')
            ->take(3)
            ->get();

        // 2. التقارير الجديدة (من النوعين)
        $recent_reports = WeeklyReport::whereHas('team', $allMyTeams)
            ->where('status', 'pending') // أو != reviewed
            ->with(['team.project.course']) // عشان نعرف هي مادة ولا تخرج
            ->latest()
            ->take(5)
            ->get();

        // 3. الاجتماعات القادمة (من النوعين)
        $upcomingMeetings = Meeting::whereHas('team', $allMyTeams)
            ->whereIn('status', ['pending', 'scheduled'])
            ->with('team')
            ->orderBy('meeting_date', 'asc')
            ->take(5)
            ->get();

        // 4. سجل الاجتماعات السابقة
        $previousMeetings = Meeting::whereHas('team', $allMyTeams)
            ->where('status', 'completed')
            ->with(['team.members', 'attendances'])
            ->orderBy('meeting_date', 'desc')
            ->take(10)
            ->get();

        return view('staff.dashboard', compact(
            'stats',
            'upcoming_defenses',
            'recent_reports',
            'upcomingMeetings',
            'previousMeetings'
        ));
    }
    
    // 2. دالة التيمات والسيرش (My Teams - Approved Only)
    public function my_teams(Request $request)
    {
        $user = Auth::user();

        // 1. ابدأ الاستعلام من موديل Team
        $query = Team::query();

        // 2. الشرط الأساسي: الدكتور يشوف تيماته المقبولة (Approved)
        // لو معيد (TA) يشوف اللي هو مشرف عليهم
        if ($user->role == 'ta') {
            $query->where('ta_id', $user->id);
        }

        // التيمات لازم تكون حالتها Approved أو In Progress عشان تظهر هنا
        $query->whereIn('proposal_status', ['approved', 'in_progress', 'completed']);

        // 3. تطبيق البحث (Search Logic)
        if ($request->has('search') && $request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%") // اسم التيم
                    ->orWhereHas('project', function ($q2) use ($searchTerm) {
                        $q2->where('proposal_title', 'LIKE', "%{$searchTerm}%"); // اسم المشروع

                    })
                    ->orWhereHas('leader', function ($q3) use ($searchTerm) {
                        $q3->where('email', 'LIKE', "%{$searchTerm}%") // إيميل الليدر
                            ->orWhere('name', 'LIKE', "%{$searchTerm}%"); // اسم الليدر
                    });
            });
        }

        // 4. فلتر السنة
        if ($request->has('year') && $request->year != 'all') {
            $query->where('year', $request->year);
        }

        // 5. هات النتايج مع العلاقات المطلوبة
        $teams = $query->with(['leader', 'members', 'project', 'memberReports'])
            ->latest()
            ->paginate(10);

        return view('staff.my_teams', compact('teams'));
    }

    // 3. دالة مراجعة البروبوزال (Proposals Review - Pending Only)
    public function proposals(Request $request)
    {
        $user = Auth::user();

        $query = Team::query();

        // 1. الشرط الأساسي: بنجيب التيمات اللي حالتها 'pending' وفيها ملف
        $query->where('proposal_status', 'pending')
            ->whereNotNull('proposal_file');

        // 2. لوجيك الصلاحيات (اختياري حسب الحاجة)
        // لو الدكتور أسامة بيشوف كله،
        if ($user->hasPermission('view_proposals')) { // عدل الشرط حسب مين اللي معاه اكسس بيرميشن
            // د. أسامة يشوف كله (تخرج + مواد)
        } else {
            // الدكاترة التانيين يشوفوا مشاريع المواد (Subject Projects) بس
            $query->whereHas('project', function ($q) {
                $q->where('type', 'subject');
            });
        }

        // --- 2. الفلاتر (Type & Year) ---
        if ($request->has('type') && $request->type != 'all') {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('type', $request->type); //  graduation
            });
        }

        if ($request->has('year') && $request->year != 'all') {
            // بنفلتر بناء على سنة الليدر
            $query->whereHas('leader', function ($q) use ($request) {
                $q->where('academic_year', $request->year);
            });
        }

        // 3. تطبيق البحث
        if ($request->has('search') && $request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%") // اسم التيم
                    ->orWhereHas('project', function ($q2) use ($searchTerm) {
                        $q2->where('proposal_title', 'LIKE', "%{$searchTerm}%"); // اسم المشروع

                    })
                    ->orWhereHas('leader', function ($q3) use ($searchTerm) {
                        $q3->where('email', 'LIKE', "%{$searchTerm}%") // إيميل الليدر
                            ->orWhere('name', 'LIKE', "%{$searchTerm}%"); // اسم الليدر
                    });
            });
        }
        // 4. الفلاتر (Year)
        if ($request->has('year') && $request->year != 'all') {
            //نفترض إن السنة موجودة في جدول اليوزر

            $query->whereHas('leader', function ($q) use ($request) {
                $q->where('academic_year', $request->year);
            });
        }

        $proposals = $query->with('leader')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // بنجيب المعيدين عشان القائمة في المودال (Modal) لو هتعين معيد
        $tas = \App\Models\User::where('role', 'ta')->get();

        return view('staff.proposals', compact('proposals', 'tas'));
    }

    // 4. دالة اتخاذ القرار في البروبوزال (Accept/Reject)
    public function decideProposal(Request $request, $id)
    {
        $team = \App\Models\Team::findOrFail($id);
        $doctorName = \Illuminate\Support\Facades\Auth::user()->name; // اسم الدكتور اللي بيعمل Approve

        if ($request->action == 'approve') {

            // 1. تجهيز البيانات
            $updateData = [
                'proposal_status' => 'approved',
                'project_phase'   => 'in_progress',
            ];

            // 2. تعيين المعيد (لو تم اختياره)
            if ($request->has('ta_id') && $request->ta_id) {
                $updateData['ta_id'] = $request->ta_id;
            }

            // 3. الحفظ في الداتابيز
            $team->update($updateData);


            // 🔥 النوتيفيكيشن للمعيد (New TA Notification)

            if (isset($updateData['ta_id'])) {
                $ta = \App\Models\User::find($updateData['ta_id']);

                if ($ta) {
                    $ta->notify(new \App\Notifications\BatuNotification([
                        'title'   => 'New Assignment 👨‍🏫',
                        'body'    => "Dr. {$doctorName} has assigned you to supervise Team: {$team->name}",
                        'icon'    => 'fas fa-chalkboard-teacher', // أيقونة معبرة عن التدريس
                        'color'   => 'text-purple-600',
                        'url'     => route('staff.team.manage', $team->id), // يروح يدير التيم علطول
                        'type'    => 'info'
                    ]));
                }
            }


            // 🔥 (اختياري) نوتيفيكيشن لليدر إن البروبوزال اتقبل

            $leader = \App\Models\User::find($team->leader_id);
            if ($leader) {
                $leader->notify(new \App\Notifications\BatuNotification([
                    'title'   => 'Proposal Approved! 🎉',
                    'body'    => "Congratulations! Your proposal for '{$team->name}' has been approved.",
                    'icon'    => 'fas fa-check-circle',
                    'color'   => 'text-green-600',
                    'url'     => route('final_project.dashboard', $team->id),
                    'type'    => 'success'
                ]));
            }

            return back()->with('success', 'Proposal approved and notifications sent.');
        } elseif ($request->action == 'reject') {

            $team->update([
                'proposal_status' => 'rejected',
                // 'rejection_reason' => $request->rejection_reason,
            ]);

            // نبلغ الليدر بالرفض
            $leader = \App\Models\User::find($team->leader_id);
            if ($leader) {
                $leader->notify(new \App\Notifications\BatuNotification([
                    'title'   => 'Proposal Update ⚠️',
                    'body'    => "Your proposal for '{$team->name}' requires revision.",
                    'icon'    => 'fas fa-exclamation-circle',
                    'color'   => 'text-red-600',
                    'url'     => route('final_project.dashboard', $team->id),
                    'type'    => 'alert'
                ]));
            }

            return back()->with('success', 'Proposal rejected.');
        }

        return back();
    }

    // 5. صفحة إدارة تيم معين
    public function manageTeam($id)
    {
        $team = Team::with(['leader', 'members', 'reports', 'memberReports'])->findOrFail($id);

        if (Auth::user()->role == 'ta' && $team->ta_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // جيب اجتماعات التيم ده بس السابقة

        $teamMeetings = \App\Models\Meeting::where('team_id', $team->id)
            ->where('status', 'completed')
            ->where('type', 'supervision')
            ->with(['attendances'])
            ->orderBy('meeting_date', 'desc')
            ->get();


        return view('staff.manage_team', compact('team', 'teamMeetings'));
    }

    // 6. جزئية رصد الدرجات (Grading System)
    public function saveGrade(Request $request, $id)
    {
        $team = \App\Models\Team::findOrFail($id);

        // التأكد من الصلاحيات
        // التحقق من صحة الأرقام
        $request->validate([
            'project_score' => 'required|numeric|min:0',
            'project_max_score' => 'required|numeric|min:1|gte:project_score',
        ]);

        // الحفظ في الداتابيز
        $team->update([
            'project_score' => $request->project_score,
            'project_max_score' => $request->project_max_score,
            'project_phase' => 'completed' // بنعتبر المشروع خلص
        ]);

        return back()->with('success', 'Project graded successfully.');
    }


    // 8. مراجعة التقرير الأسبوعي
    public function reviewReport(Request $request, $report_id)
    {
        $report = WeeklyReport::findOrFail($report_id);

        $report->update([
            'ta_feedback' => $request->ta_feedback,
            'status' => 'reviewed'
        ]);

        return back()->with('success', 'Feedback submitted successfully.');
    }

    // 9. تحديد موعد المناقشة
    public function scheduleDefense(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        // التحقق من الصلاحيات
        if (Auth::user()->role == 'ta' && $team->ta_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            // تم تغيير after:today إلى date فقط أو after_or_equal لضمان قبول ميعاد اليوم
            'defense_date' => 'required|date',
            'defense_location' => 'required|string|max:255',
        ]);

        try {
            $team->update([
                'defense_date' => $request->defense_date,
                'defense_location' => $request->defense_location,
                'project_phase' => 'ready_for_defense'
            ]);


            // نلف على كل أعضاء التيم نبلغهم
            foreach ($team->members as $member) {
                $student = \App\Models\User::find($member->user_id);

                if ($student) {
                    $student->notify(new \App\Notifications\BatuNotification([
                        'title'   => 'Defense Date Set 🎓',
                        'body'    => 'Get ready! Defense on ' . date('d M, h:i A', strtotime($request->defense_date)),
                        'icon'    => 'fas fa-graduation-cap',
                        'color'   => 'text-red-600',
                        'url'     => route('projects.show', $team->project_id),
                        'type'    => 'info'
                    ]));
                }
            }

            return back()->with('success', 'Defense scheduled successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to save: ' . $e->getMessage()]);
        }
    }
    // 10. View As Student
    // public function viewTeamAsStudent($id)
    // {
    //     $team = Team::with(['leader', 'members', 'tasks', 'reports', 'memberReports'])->findOrFail($id);
    //     $project = $team->project;
    //     $isViewAs = true;
    //     $myRole = 'viewer';

    //     $fundsHistory = collect([]);
    //     $activeFund = null;
    //     if (class_exists(\App\Models\ProjectExpense::class)) {
    //         $fundsHistory = \App\Models\ProjectExpense::where('team_id', $team->id)
    //             ->with('contributions.user')
    //             ->latest()
    //             ->get();
    //         $activeFund = \App\Models\ProjectExpense::where('team_id', $team->id)
    //             ->latest()
    //             ->first();
    //     }

    //     $canReport = false;

    //     return view('final_project.dashboard', compact(
    //         'team',
    //         'project',
    //         'isViewAs',
    //         'myRole',
    //         'fundsHistory',
    //         'activeFund',
    //         'canReport'
    //     ));
    // }

    public function viewTeamAsStudent($id)
    {
        // 1. تحميل التيم
        $team = \App\Models\Team::with([
            'leader',
            'members',
            'tasks.user',
            'reports',
            'memberReports',
            'meetings'
        ])->findOrFail($id);

        $project = $team->project;
        $isViewAs = true;
        $myRole = 'leader';

        // 2. تجهيز بيانات "لمّ الفلوس" (Funds) ✅ تصحيح الخطأ هنا
        $fundsHistory = collect([]);
        $activeFund = null;

        if (class_exists(\App\Models\ProjectFund::class)) {
            // كنا بننادي على ProjectExpense غلط، الصح ProjectFund
            $fundsHistory = \App\Models\ProjectFund::where('team_id', $team->id)
                ->with('contributions.user') // عشان نشوف مين دفع في اللمة دي
                ->latest()
                ->get();

            $activeFund = $fundsHistory->first();
        }

        // 3. تجهيز بيانات "المصاريف" (Expenses)
        // لازم نبعتها لوحدها عشان الدكتور يشوف الفواتير برضه
        $expensesHistory = collect([]);
        if (class_exists(\App\Models\ProjectExpense::class)) {
            $expensesHistory = \App\Models\ProjectExpense::where('team_id', $team->id)
                ->latest()
                ->get();
        }

        // 4. التاسكات
        $myTasks = $team->tasks;
        $canReport = false;

        return view('final_project.dashboard', compact(
            'team',
            'project',
            'isViewAs',
            'myRole',
            'fundsHistory',     // ✅ دي بتشغل مودال الـ Funds History
            'expensesHistory',  // ✅ دي زيادة عشان لو عايز تعرض المصاريف في مكان تاني
            'activeFund',
            'canReport',
            'myTasks'
        ));
    }

    // دالة لعرض الملفات (التقارير، الإيصالات، إلخ) للستاف
    public function viewAttachment($path)
    {
        // Use Storage facade for secure serving and correct headers
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
    }

    // 11. الرد على الاجتماع
    public function respondMeeting(Request $request, $id)
    {
        $meeting = \App\Models\Meeting::findOrFail($id);

        $request->validate([
            'status' => 'required|in:confirmed,rejected',
        ]);

        if ($request->status == 'confirmed') {
            $request->validate([
                'meeting_link' => 'nullable|required_if:mode,online|url',
                'location' => 'nullable|required_if:mode,offline|string|max:255',
            ]);

            if ($meeting->mode == 'online') {
                $meeting->meeting_link = $request->meeting_link;
                $meeting->location = null;
            } else {
                $meeting->location = $request->location;
                $meeting->meeting_link = null;
            }
        }

        $meeting->status = $request->status;
        $meeting->save();

        // نجيب الليدر بتاع التيم صاحب الاجتماع
        $leader = \App\Models\User::find(\App\Models\Team::find($meeting->team_id)->leader_id);

        if ($leader) {
            // نحدد لون ونص الرسالة حسب الحالة
            $statusMsg = $request->status == 'confirmed' ? 'Confirmed ✅' : 'Rejected ❌';
            $color = $request->status == 'confirmed' ? 'text-green-500' : 'text-red-500';

            $leader->notify(new \App\Notifications\BatuNotification([
                'title'   => 'Meeting Update 📢',
                'body'    => 'Your meeting request regarding "' . $meeting->topic . '" has been ' . $statusMsg,
                'icon'    => 'fas fa-envelope-open-text',
                'color'   => $color,
                'url'     => route('final_project.dashboard', $meeting->team_id),
                'type'    => 'info'
            ]));
        }

        return back()->with('success', 'Meeting request updated successfully.');
    }

    public function confirmMeeting(Request $request, $id)
    {
        $meeting = \App\Models\Meeting::findOrFail($id);
        $meeting->meeting_link = $request->link; // احفظ اللينك
        $meeting->status = 'scheduled';
        $meeting->save();

        return back();
    }

    // 1. إنهاء الاجتماع لأول مرة
    public function endMeeting(Request $request, $id)
    {
        $meeting = \App\Models\Meeting::findOrFail($id);

        if ($request->has('attendance')) {
            foreach ($request->attendance as $userId => $statusValue) {
                // تحويل 'present' إلى true (1) و 'absent' إلى false (0)
                $isPresent = ($statusValue === 'present' || $statusValue == '1') ? true : false;

                \App\Models\MeetingAttendance::updateOrCreate(
                    [
                        'meeting_id' => $meeting->id,
                        'user_id'    => $userId
                    ],
                    [
                        'is_present' => $isPresent
                    ]
                );
            }
        }

        $meeting->status = 'completed';
        $meeting->save();

        // السطر ده لازم يكون "خارج" الـ foreach عشان يلف على كل الطلاب
        return back()->with('success', 'Meeting record secured and attendance saved.');
    }



    // 2. تحديث الحضور من الأرشيف (Log)
    public function updateAttendance(Request $request, $id)
    {
        $meeting = \App\Models\Meeting::findOrFail($id);

        if ($request->has('attendance')) {
            foreach ($request->attendance as $userId => $statusValue) {

                // في الأرشيف القيمة بتيجي 1 أو 0 من الـ x-model
                $isPresent = ($statusValue == '1' || $statusValue == 1 || $statusValue === 'present') ? true : false;

                \App\Models\MeetingAttendance::updateOrCreate(
                    [
                        'meeting_id' => $meeting->id,
                        'user_id'    => $userId
                    ],
                    [
                        'is_present' => $isPresent
                    ]
                );
            }
        }

        return back()->with('success', 'Archive synchronized successfully.');
    }


    public function updateDeadline(Request $request, $id)
    {
        $request->validate([
            'deadline' => 'required|date',
        ]);

        // بنجيب المشروع ونحدث الديدلاين
        $project = \App\Models\Project::findOrFail($id);
        $project->update([
            'deadline' => $request->deadline
        ]);

        return back()->with('success', 'Deadline updated successfully. Teams will be locked after this date.');
    }



    public function defenseCalendar()
    {
        // 🎨 مصفوفة ألوان "مودرن" ومتنوعة
        $palette = [
            '#6366f1', // Indigo
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#f43f5e', // Rose
            '#f97316', // Orange
            '#eab308', // Yellow
            '#22c55e', // Green
            '#14b8a6', // Teal
            '#06b6d4', // Cyan
            '#3b82f6', // Blue
            '#64748b', // Slate
        ];

        $scheduledTeams = \App\Models\Team::whereNotNull('defense_date')
            ->with('project') // 1. لازم نجيب المشروع عشان نعرف نوعه (type)
            ->get()
            ->map(function ($team) use ($palette) {

                // 2. هنا بنحدد اللينك حسب نوع المشروع
                $url = '#'; // قيمة افتراضية

                if ($team->project && $team->project->type == 'graduation') {

                    $url = route('staff.team.manage', $team->id);
                } else {

                    $url = route('staff.team.view', $team->id);
                }
                // 🎨 اختيار لون بناءً على ID التيم (عشان يفضل ثابت لنفس التيم)
                // بنستخدم باقي القسمة (%) عشان نلف على الألوان لو التيمات أكتر من عدد الألوان
                $colorIndex = $team->id % count($palette);
                $teamColor = $palette[$colorIndex];
                return [
                    'title' => $team->name . ' (' . ($team->project->title ?? 'Project') . ')',
                    'start' => $team->defense_date,
                    'url' => $url, // استخدمنا المتغير اللي حسبناه فوق
                    'extendedProps' => [
                        'location' => $team->defense_location ?? 'TBD',
                        'members_count' => $team->members->count(),
                        // ممكن نبعت اللون كمان عشان نميزهم في الجدول
                        'type' => $team->project->type ?? 'general'
                    ],
                    // نغير اللون: التخرج ذهبي/برتقالي، المواد أزرق/بنفسجي
                    // اللون هنا بقا ديناميك
                    'backgroundColor' => $teamColor,
                    'borderColor' => $teamColor,     // لون الإطار نفس اللون
                    'textColor' => '#ffffff'
                ];
            });

        return view('staff.doctor_timetable', compact('scheduledTeams'));
    }


    public function exportTeams(Request $request)
    {
        $teamIds = json_decode($request->teams);

        if (empty($teamIds)) {
            return back()->with('error', 'No teams selected.');
        }

        // 1. استقبال الاسم من الريكوست
        $customName = $request->input('file_name');

        // 2. تجهيز اسم الملف
        if ($customName) {
            $cleanName = \Illuminate\Support\Str::slug(str_replace('.xlsx', '', $customName), '_');
            $fileName = $cleanName . '.xlsx';
        } else {
            $fileName = 'Teams_Report_' . date('Y-m-d_H-i') . '.xlsx';
        }


        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TeamsExport($teamIds), $fileName);
    }
}
