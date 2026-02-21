<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// استدعاء الموديلات
use App\Models\User;
use App\Models\Course;
use App\Models\Project;
use App\Models\Team;
use App\Models\Meeting;
// استدعاء المتحكمات (Controllers)
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FinalProjectController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\JoinRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Main Landing Page
Route::get('/', [JoinRequestController::class, 'index'])->name('welcome');

// Join Request Form Routes
Route::get('/join', [JoinRequestController::class, 'create'])->name('join.create');
Route::post('/join', [JoinRequestController::class, 'store'])->name('join.store');
Route::get('/join/check-duplicate', [JoinRequestController::class, 'checkDuplicate'])->name('join.checkDuplicate');
Route::get('/join/success', function () {
    return view('join_requests.success');
})->name('join.success');

require __DIR__ . '/auth.php';

// ====================================================
// منطقة الأعضاء المسجلين (Authenticated Routes)
// ====================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Join Requests (Restricted by Email)
    Route::get('/join-requests/export', [JoinRequestController::class, 'export'])->name('join.export');
    Route::get('/join-requests', [JoinRequestController::class, 'adminIndex'])->name('join.admin');
    Route::get('/join-requests/{id}/approve', [JoinRequestController::class, 'approve'])->name('join.approve');
    Route::post('/join-requests/{id}/reject', [JoinRequestController::class, 'reject'])->name('join.reject');
    Route::post('/join-requests/{id}/store-user', [JoinRequestController::class, 'storeUser'])->name('join.storeUser');

    // 1. الداشبورد والبروفايل
    Route::get('/dashboard', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');
    
    // New Detailed Profile Routes
    Route::get('/profile/{id?}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update-details', [ProfileController::class, 'updateDetails'])->name('profile.update_details');
    
    // Legacy Breeze Routes (kept for compatibility if needed, or commented out)
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. نظام المشاريع (Projects)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // 3. نظام الفرق (Teams)
    Route::post('/teams/store', [TeamController::class, 'store'])->name('teams.store');
    Route::post('/teams/join', [TeamController::class, 'join'])->name('teams.join');
    Route::post('/teams/invite', [TeamController::class, 'invite'])->name('teams.invite');
    Route::post('/teams/leave', [TeamController::class, 'leave'])->name('teams.leave');

    // --> راوتات إدارة الأعضاء (نقلناها هنا عشان تشتغل) <--
    Route::post('/teams/remove-member', [TeamController::class, 'removeMember'])->name('teams.removeMember');
    Route::post('/teams/promote-member', [TeamController::class, 'promoteMember'])->name('teams.promoteMember');
    Route::post('/teams/report-member', [TeamController::class, 'reportMember'])->name('teams.reportMember');

    Route::get('/teams/project/download/{id}', [TeamController::class, 'downloadProject'])->name('teams.download');
    // 4. نظام المهام (Tasks Workflow)
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/{id}/submit', [TaskController::class, 'submit'])->name('tasks.submit');
    Route::post('/tasks/{id}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::post('/tasks/{id}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks/{id}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{id}/download', [TaskController::class, 'download'])->name('tasks.download');
    // 5. نظام الإشعارات (Notifications)
    Route::get('/notifications/{id}/accept', [TeamController::class, 'acceptInvite'])->name('notifications.accept');
    Route::get('/notifications/{id}/reject', [TeamController::class, 'rejectInvite'])->name('notifications.reject');

    Route::get('/notifications/check', function () {
        return response()->json([
            'count' => Auth::check() ? Auth::user()->unreadNotifications->count() : 0
        ]);
    })->name('notifications.check');

    Route::post('/notifications/mark-read', function () {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
        }
        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');

    Route::get('/notifications/read-all', function () {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return back();
    })->name('notifications.readAll');

    // 6. تسليم المشروع النهائي
    Route::post('/teams/submit', [TeamController::class, 'submitProject'])->name('teams.submit');

    //Final project:
    Route::middleware(['auth', 'verified'])->group(function () {
        // 1. زرار البداية
        Route::get('/final-project/start', [FinalProjectController::class, 'start'])->name('final_project.start');

        // 2. صفحة إنشاء التيم (لو حبيت تروح لها مباشر)
        Route::get('/final-project/create/{project}', [FinalProjectController::class, 'createTeamView'])->name('final_project.create');

        // 3. داشبورد الفاينل (دي الصفحة الكبيرة اللي هنشتغل عليها بعدين)
        Route::get('/final-project/team/{team}', [FinalProjectController::class, 'dashboard'])->name('final_project.dashboard');
        Route::post('/final-project/store', [FinalProjectController::class, 'storeTeam'])->name('final_project.store');
        Route::post('/final-project/leave', [FinalProjectController::class, 'leaveTeam'])->name('final_project.leave');

        // ضيف دول عشان الزراير تشتغل وما يطلعش الايرور ده تاني
        // مسارات مشروع التخرج
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('/final-project/start', [FinalProjectController::class, 'start'])->name('final_project.start');

            // 👇👇 ده السطر اللي ناقص أو اللي الفورم مش لاقيه 👇👇
            Route::post('/final-project/store', [FinalProjectController::class, 'storeTeam'])->name('final_project.store');

            // ودول كمان عشان باقي الزراير تشتغل
            Route::post('/final-project/join', [FinalProjectController::class, 'joinTeam'])->name('final_project.join');
            Route::post('/final-project/leave', [FinalProjectController::class, 'leaveTeam'])->name('final_project.leave');

            // ====================================================
            // 🎓 Final Project Routes (مشروع التخرج)
            // ====================================================

            // 1. الصفحات (GET)
            Route::get('/final-project/start', [FinalProjectController::class, 'start'])->name('final_project.start');
            // راوت التعديل (الجديد)
            Route::post('/final-project/update-logo', [FinalProjectController::class, 'updateLogo'])->name('final_project.update_logo');
            Route::get('/final-project/team/{team}', [FinalProjectController::class, 'dashboard'])->name('final_project.dashboard');
            Route::get('/final-project/logo/{id}', [FinalProjectController::class, 'getTeamLogo'])->name('final_project.logo');
            // 2. الأكشنز الأساسية (POST)
            Route::post('/final-project/store', [FinalProjectController::class, 'storeTeam'])->name('final_project.store');
            Route::post('/final-project/join', [FinalProjectController::class, 'joinTeam'])->name('final_project.join');
            Route::post('/final-project/toggle-group', [FinalProjectController::class, 'toggleGroup'])->name('final_project.toggle_group');
            Route::post('/final-project/leave', [FinalProjectController::class, 'leaveTeam'])->name('final_project.leave');

            // 3. الأكشنز الخاصة بالأعضاء (Invite, Remove, Report, Approve, Reject)
            Route::post('/final-project/member/{id}/approve', [FinalProjectController::class, 'approveMember'])->name('final_project.approve_member');
            Route::post('/final-project/member/{id}/reject', [FinalProjectController::class, 'rejectMember'])->name('final_project.reject_member');
            
            Route::post('/final-project/invite', [FinalProjectController::class, 'inviteMember'])->name('final_project.invite');
            Route::post('/final-project/remove-member', [FinalProjectController::class, 'removeMember'])->name('final_project.remove_member');
            Route::post('/final-project/report-member', [FinalProjectController::class, 'reportMember'])->name('final_project.reportMember');

            Route::post('/final-project/submit-proposal', [FinalProjectController::class, 'submitProposal'])->name('final_project.submitProposal');
            Route::get('/final-project/proposal/view/{team_id}', [FinalProjectController::class, 'viewProposalFile'])
                ->name('proposal.view_file');
            // 🧪 راوت للتجربة فقط: تغيير حالة مشروع التخرج
            Route::get('/test/approve-team/{id}/{status}', function ($id, $status) {
                $team = Team::find($id);
                if (!$team) return "تيم مش موجود";

                $team->update([
                    'proposal_status' => $status,
                    'rejection_reason' => $status == 'rejected' ? 'Idea is not clear, please add more details about AI model.' : null
                ]);

                return redirect()->route('final_project.dashboard', $id)->with('success', "تم تغيير الحالة إلى: $status");
            });

            Route::post('/final-project/update-member', [FinalProjectController::class, 'updateMemberStatus'])->name('final_project.updateMember');

            Route::post('/final-project/expenses', [FinalProjectController::class, 'storeExpense'])->name('final_project.storeExpense');

            Route::post('/final-project/funds/create', [FinalProjectController::class, 'storeFund'])->name('final_project.storeFund');
            Route::post('/final-project/funds/submit', [FinalProjectController::class, 'submitPayment'])->name('final_project.submitPayment');
            Route::post('/final-project/funds/review', [FinalProjectController::class, 'reviewPayment'])->name('final_project.reviewPayment');
            // Route::post('/final-project/funds/pay', [FinalProjectController::class, 'markPaid'])->name('final_project.markPaid'); // Deprecated


            Route::post('/final-project/reports', [FinalProjectController::class, 'storeReport'])->name('final_project.storeReport');

            Route::post('/final-project/meetings/supervision', [FinalProjectController::class, 'requestSupervisionMeeting'])->name('final_project.requestSupervision');
            Route::post('/final-project/meetings/internal', [FinalProjectController::class, 'storeInternalMeeting'])->name('final_project.storeInternalMeeting');
            Route::post('/final-project/meetings/attendance', [FinalProjectController::class, 'markAttendance'])->name('final_project.markAttendance');
            // 🧪 راوت للتجربة: الموافقة الفورية على أي ميتينج
            Route::get('/test/approve-meeting/{id}', function ($id) {
                Meeting::where('id', $id)->update(['status' => 'confirmed']);
                return back()->with('success', 'Meeting Confirmed via Magic Link! 🪄');
            });

            Route::post('/final-project/gallery/upload', [FinalProjectController::class, 'uploadGallery'])->name('final_project.uploadGallery');
            Route::post('/final-project/gallery/delete/{id}', [FinalProjectController::class, 'deleteGallery'])->name('final_project.deleteGallery');

            Route::post('/final-project/{id}/request-review', [FinalProjectController::class, 'requestPreDefense'])->name('final_project.request_review');

            Route::get('/fix-project-status/{id}', function ($id) {
                $project = Project::findOrFail($id);
                $project->update(['status' => 'in_progress']);
                return "تم إصلاح حالة المشروع رقم " . $id . " ورجع in_progress";
            });
            Route::get('/phpinfo', function () {
                return phpinfo();
            });
            Route::post('/final-project/{id}/submit-final', [FinalProjectController::class, 'submitFinalProject'])
                ->name('final_project.submit_final');
        });
    });


    // ====================================================
    // 🆕 Weekly Evaluation System (Leaders Only)
    // ====================================================
    Route::middleware(['auth'])->group(function() {
        Route::get('/weekly-evaluation/get/{studentId}/{week}', [App\Http\Controllers\WeeklyEvaluationController::class, 'getWeekData'])->name('weekly_evaluation.get');
        Route::post('/weekly-evaluation/store', [App\Http\Controllers\WeeklyEvaluationController::class, 'store'])->name('weekly_evaluation.store');
        Route::get('/weekly-evaluation/download/{id}', [App\Http\Controllers\WeeklyEvaluationController::class, 'downloadPdf'])->name('weekly_evaluation.download');
    });

    // Wallet System
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/search', [App\Http\Controllers\WalletController::class, 'search'])->name('wallet.search');
    Route::post('/wallet/transaction', [App\Http\Controllers\WalletController::class, 'transact'])->name('wallet.transact');

});



//Sttaf controller
// --- مجموعة راوتات الدكاترة والمعيدين (Staff) ---
//Sttaf controller
// --- مجموعة راوتات القادة والمعيدين (Leader & TA) ---
Route::middleware(['auth', 'role:admin,ta'])->prefix('staff')->group(function () {

    // ====================================================
    // 1. راوتات عامة (متاحة لكل الطاقم)
    // ====================================================
    // Route للداشبورد الرئيسية
    Route::get('/staff/dashboard', [StaffController::class, 'index'])->name('staff.dashboard');
    // 1. الداشبورد الرئيسية (مكرر)
    Route::get('/dashboard', [StaffController::class, 'index'])->name('staff.dashboard');

    Route::get('/defense-calendar', [StaffController::class, 'defenseCalendar'])->name('staff.doctor_timetable');


    // ====================================================
    // 2. إدارة المقترحات (View Proposals)
    // ====================================================
    Route::middleware('permission:view_proposals')->group(function () {
        // 2. إدارة المقترحات (Proposals)
        Route::get('/proposals', [StaffController::class, 'proposals'])->name('staff.proposals');
        Route::post('/proposals/{id}/decide', [StaffController::class, 'decideProposal'])->name('staff.proposals.decide');
        Route::get('/staff/proposals', [StaffController::class, 'proposals'])->name('staff.proposals');
        Route::get('/proposal/view/{team_id}', [FinalProjectController::class, 'viewProposalFile'])
            ->name('proposal.view_file');
    });


    // ====================================================
    // 3. إدارة التيمات (Manage Teams)
    // ====================================================
    Route::middleware('permission:manage_teams')->group(function () {
        // Route لصفحة التيمات
        Route::get('/staff/my-teams', [StaffController::class, 'my_teams'])->name('staff.my_teams');
        Route::get('/my-teams', [StaffController::class, 'myTeams'])->name('staff.my_teams');
        Route::post('/staff/teams/export', [App\Http\Controllers\StaffController::class, 'exportTeams'])->name('staff.teams.export');
        // 3. إدارة تيم محدد
        Route::get('/my-teams/{id}', [StaffController::class, 'manageTeam'])->name('staff.team.manage');

        // 4. إضافة تاسك
        Route::post('/my-teams/{id}/tasks', [StaffController::class, 'storeTask'])->name('staff.team.task.store');

        // حفظ فيدباك التقرير
        Route::post('/report/{id}/review', [StaffController::class, 'reviewReport'])->name('staff.report.review');

        //راوت الميتنج والردود
        Route::post('/meeting/{id}/respond', [StaffController::class, 'respondMeeting'])->name('staff.meeting.respond');
        Route::post('/staff/meeting/{id}/end', [StaffController::class, 'endMeeting'])->name('staff.meeting.end');
        Route::put('/meetings/{id}/attendance', [StaffController::class, 'updateAttendance'])->name('meetings.update_attendance');
        Route::post('/meeting/{id}/end', [StaffController::class, 'endMeeting'])->name('meeting.end');
    });


    // ====================================================
    // 4. إدارة المواد والدرجات (Manage Subjects)
    // ====================================================
    Route::middleware('permission:manage_subjects')->group(function () {
        // راوت وضع المشاهدة
        Route::get('/team/{id}/view-as-student', [StaffController::class, 'viewTeamAsStudent'])->name('staff.team.view_as');
        Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {

            // ... راوتات الستاف التانية ...
        });

        // راوت تحديث الديدلاين
        Route::put('/staff/project/{id}/deadline', [App\Http\Controllers\StaffController::class, 'updateDeadline'])->name('staff.project.deadline');

        Route::post('/team/{id}/grade', [App\Http\Controllers\StaffController::class, 'saveGrade'])->name('staff.team.grade');

        // 1. راوت صفحة الكروت (My Courses)
        Route::get('/subjects', [App\Http\Controllers\StaffSubjectController::class, 'index'])->name('subjects.index');

        // 2. راوت صفحة الإدارة (Manage Course)
        Route::get('/subjects/{id}/manage', [App\Http\Controllers\StaffSubjectController::class, 'manage'])->name('subjects.manage');

        Route::get('/staff/team/{id}/view', [App\Http\Controllers\StaffSubjectController::class, 'viewTeam'])->name('staff.team.view');
        Route::post('/staff/team/{id}/individual-grades', [App\Http\Controllers\StaffSubjectController::class, 'saveIndividualGrades'])->name('staff.team.save_individual_grades');

        // ✅ التصحيح: استخدم StaffProjectController بدل StaffSubjectController
        Route::get('/team/{id}/view', [App\Http\Controllers\StaffSubjectController::class, 'viewTeam'])->name('staff.team.view');
        Route::post('/team/{id}/individual-grades', [App\Http\Controllers\StaffSubjectController::class, 'saveIndividualGrades'])->name('staff.team.save_individual_grades');
        // الراوت الجديد لحفظ كل الدرجات
        Route::post('/staff/team/{id}/save-all-grades', [App\Http\Controllers\StaffSubjectController::class, 'saveAllGrades'])->name('staff.team.save_all_grades');
        Route::post('/staff/subject/teams/export', [App\Http\Controllers\StaffSubjectController::class, 'exportTeams'])->name('staff.subject.teams.export');
    });


    // ====================================================
    // 5. إدارة المناقشات (View Defense)
    // ====================================================
    Route::middleware('permission:view_defense')->group(function () {
        // تحديد المناقشة
        Route::post('/team/{id}/defense', [StaffController::class, 'scheduleDefense'])->name('staff.team.defense');
    });


    // ====================================================
    // 6. أدوات النظام (System - Admin Only)
    // ====================================================
    Route::middleware('permission:manage_academic_control')->group(function () {
        Route::post('/system/change-term', [App\Http\Controllers\SystemController::class, 'changeTerm'])->name('staff.system.change_term');
        Route::post('/system/promote', [App\Http\Controllers\SystemController::class, 'promoteStudents'])->name('staff.system.promote');
    });


    // ====================================================
    // 7. إدارة المستخدمين (User Management)
    // ====================================================

    // أ. النسخ الاحتياطي (Backup DB)
    Route::middleware('permission:backup_db')->group(function () {
        Route::get('/database/export', [AdminUserController::class, 'exportDatabase'])->name('admin.database.export');
    });

    // ب. سجل النشاطات (Activity Logs)
    Route::middleware('permission:view_activity_log')->group(function () {
        Route::get('/admin/activity-logs', [AdminUserController::class, 'activityLogs'])->name('admin.activity_logs');
    });

    // ج. باقي عمليات إدارة المستخدمين (Manage Users) - [MODIFIED: Only Admin]
    Route::middleware(['permission:manage_users', 'role:admin'])->group(function () {
        Route::get('/staff/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::post('/staff/admin/users/store', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('/staff/admin/users/delete/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.delete');
        Route::post('/staff/admin/users/import', [AdminUserController::class, 'import'])->name('admin.users.import');
        Route::get('/admin/users/import/sample', [AdminUserController::class, 'downloadSampleCsv'])->name('admin.users.import.sample');

        Route::put('/users/update/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::post('/admin/users/export', [AdminUserController::class, 'exportSelectedUsers'])->name('admin.users.export');

        // العمليات اللي كانت خارج الجروب (ضمناها للحماية)
        Route::get('/users/{id}/restore', [AdminUserController::class, 'restore'])->name('admin.users.restore');
        Route::delete('/users/{id}/force-delete', [AdminUserController::class, 'forceDelete'])->name('admin.users.force_delete');
        Route::post('/admin/users/bulk-update', [AdminUserController::class, 'bulkUpdate'])->name('admin.users.bulk_update');
        Route::post('/admin/users/bulk-delete', [AdminUserController::class, 'bulkDelete'])->name('admin.users.bulk_delete');
        Route::post('/admin/users/bulk-trash-action', [AdminUserController::class, 'bulkTrashAction'])->name('admin.users.bulk_trash_action');
        Route::delete('/admin/users/bulk-force-delete', [AdminUserController::class, 'bulkForceDelete'])->name('admin.users.bulk_force_delete');
    });
    // --- Admin Team Management ---
    // --- Admin Team Management (Database) ---
    // غيرنا role:admin لـ permission:manage_teams_db
    Route::middleware(['auth', 'permission:manage_teams_db'])->group(function () {

        // الصفحة الرئيسية للجدول
        Route::get('/admin/teams-database', [App\Http\Controllers\AdminTeamController::class, 'index'])->name('admin.teams.index');

        // حذف عضو وحذف التيم
        Route::delete('/admin/teams/{team_id}/member/{user_id}', [App\Http\Controllers\AdminTeamController::class, 'removeMember'])->name('admin.teams.remove_member');
        Route::delete('/admin/teams/{id}', [App\Http\Controllers\AdminTeamController::class, 'destroy'])->name('admin.teams.delete');
    });
});



// ده راوت مؤقت عشان ننقذ الموقف
Route::get('/run-magic-seeder', function () {
    try {
        // 1. تشغيل الميجريشن عشان نتأكد إن الجداول موجودة
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

        // 2. تشغيل السيدر
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);

        return '<h1>تم يا جيمي! الداتا نزلت بنجاح 🎉</h1>';
    } catch (\Exception $e) {
        return '<h1>حصل مشكلة: </h1>' . $e->getMessage();
    }
});




// ====================================================
// أدوات المطورين (Setup Demo)
// ====================================================
// Route::get('/setup-demo', function () {
//     // 1. تنظيف الجداول
//     DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//     User::truncate();
//     Course::truncate();
//     Project::truncate();
//     DB::table('course_user')->truncate();
//     DB::table('teams')->truncate();
//     DB::table('team_members')->truncate();
//     DB::table('tasks')->truncate();
//     DB::table('notifications')->truncate();
//     DB::table('reports')->truncate();
//     DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//     // 2. إنشاء الطلاب
//     $omar = User::create(['name' => 'Omar Hassan', 'email' => '2420823@batechu.com', 'password' => Hash::make('12345678'), 'university_email' => '2420823@batechu.com']);
//     $ahmed = User::create(['name' => 'Ahmed Sultan', 'email' => '2000001@batechu.com', 'password' => Hash::make('12345678'), 'university_email' => '2000001@batechu.com']);

//     // 3. إنشاء المواد
//     $web = Course::create(['name' => 'Web Programming I', 'code' => 'IT201', 'color' => 'purple', 'icon_class' => 'fas fa-code']);
//     $linux = Course::create(['name' => 'Linux Essentials', 'code' => 'IT202', 'color' => 'orange', 'icon_class' => 'fas fa-terminal']);
//     $db = Course::create(['name' => 'Introduction to DB', 'code' => 'IT203', 'color' => 'blue', 'icon_class' => 'fas fa-database']);
//     $network = Course::create(['name' => 'Network Fundamentals', 'code' => 'IT204', 'color' => 'green', 'icon_class' => 'fas fa-network-wired']);

//     // 4. تسجيل المواد (لكل الطلاب)
//     $ids = [$web->id, $linux->id, $db->id, $network->id];
//     $omar->courses()->attach($ids);
//     $ahmed->courses()->attach($ids);

//     // 5. مشاريع
//     Project::create(['title' => 'LMS System Full Stack', 'type' => 'subject', 'course_id' => $web->id, 'deadline' => '2025-12-30']);
//     Project::create(['title' => 'Automated Backup Script', 'type' => 'subject', 'course_id' => $linux->id, 'deadline' => '2025-11-20']);

//     return "تم تجهيز الداتا بنجاح!";
// });

// // راوت إضافي (اختياري) لتسجيل أحمد لو حصل خطأ
// Route::get('/enroll-ahmed', function () {
//     $ahmed = User::where('email', '2000001@batechu.com')->first();
//     if (!$ahmed) return "أحمد مش موجود!";
//     $ahmed->courses()->syncWithoutDetaching([1, 2, 3, 4]);
//     return "تم!";
// });

require __DIR__ . '/test_pdf.php';

Route::get('/fix-images', function () {
    $path = storage_path('app/public');
    $link = public_path('storage');
    
    // 1. Try to link
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $artisan = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        $artisan = 'Error: ' . $e->getMessage();
    }
    
    // 2. Check files
    $files = [];
    if (is_dir($path)) {
        try {
            $files = scandir($path); 
        } catch (\Exception $e) {
            $files = 'Error scanning ' . $path;
        }
        // Check specific folder
        $photosPath = $path . '/join_requests_photos';
        if (is_dir($photosPath)) {
            $photos = scandir($photosPath);
        } else {
            $photos = 'Directory not found: ' . $photosPath;
        }
    } else {
        $photos = 'Storage root not found at ' . $path;
    }

    return [
        'symlink_exists' => file_exists($link),
        'symlink_valid' => is_link($link),
        'symlink_target' => is_link($link) ? readlink($link) : 'N/A',
        'real_storage_path' => $path,
        'artisan_output' => $artisan,
        'root_files' => $files,
        'join_requests_photos_content' => $photos,
        'app_url' => env('APP_URL'),
        'asset_test' => asset('storage/test.jpg'),
    ];
});

