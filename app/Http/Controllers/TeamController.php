<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Notifications\BatuNotification; // استدعاء الإشعارات
use App\Models\Task;
// لا تنسى تعمل import فوق للمكتبة دي
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class TeamController extends Controller
{
    // 1. إنشاء تيم جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        $user = Auth::user();

        // إنشاء التيم
        $team = Team::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'code' => strtoupper(Str::random(6)),
            'leader_id' => $user->id,
            'status' => 'forming'
        ]);

        // إضافة الليدر كعضو
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'leader'
        ]);

        return redirect()->back()->with('success', 'Team created successfully!');
    }

    // 2. الانضمام بالكود
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        // 1. تحويل الكود لحروف كبيرة وتنظيف المسافات
        $code = strtoupper(trim($request->code));

        // 2. البحث عن التيم
        $team = Team::where('code', $code)
            ->where('project_id', $request->project_id)
            ->first();

        if (!$team) {
            // لو الكود غلط، نرجع برسالة خطأ صريحة
            return back()->withErrors(['code' => 'Team code is incorrect! Please check again.']);
        }

        // 3. التأكد من العدد
        if ($team->members()->count() >= 100) {
            return back()->withErrors(['msg' => 'This team is full.']);
        }

        $user = Auth::user();

        // 4. الإضافة
        \App\Models\TeamMember::firstOrCreate([
            'team_id' => $team->id,
            'user_id' => $user->id
        ], ['role' => 'member']);

        // 5. توجيه إجباري لصفحة المشروع عشان يشوف التغيير
        return redirect()->route('projects.show', $request->project_id)
            ->with('success', 'Joined team successfully!');
    }

    // 3. إرسال دعوة (Invite)
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = Auth::user();
        $team = Team::where('leader_id', $user->id)->latest()->first();

        if (!$team) {
            return back()->withErrors(['msg' => 'You are not a leader!']);
        }

        // شرط الـ 100 أعضاء
        if ($team->members()->count() >= 100) {
            return back()->withErrors(['msg' => 'Team is full! Maximum 100 members allowed.']);
        }

        $invitedUser = \App\Models\User::where('email', $request->email)->first();

        if (TeamMember::where('team_id', $team->id)->where('user_id', $invitedUser->id)->exists()) {
            return back()->withErrors(['email' => 'User is already in the team!']);
        }

        // إرسال الإشعار
        $invitedUser->notify(new BatuNotification([
            'title'   => 'Team Invitation',
            'body'    => "{$user->name} invited you to join team: {$team->name}",
            'icon'    => 'fas fa-user-plus',
            'color'   => 'text-[#266963]',
            'type'    => 'action',
            'team_id' => $team->id,
            'url'     => '#'
        ]));

        return back()->with('success', 'Invitation sent successfully!');
    }

    // 4. قبول الدعوة (Accept) - (تم التصحيح هنا) ✅
    public function acceptInvite($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $data = $notification->data;

        if (isset($data['team_id'])) {
            $teamId = $data['team_id'];

            // التصحيح: حساب العدد الحالي قبل الشرط
            $currentCount = TeamMember::where('team_id', $teamId)->count();

            if ($currentCount >= 100) {
                return back()->withErrors(['msg' => 'Sorry, this team is now full.']);
            }

            // إضافة العضو
            TeamMember::firstOrCreate([
                'team_id' => $teamId,
                'user_id' => $user->id,
            ], ['role' => 'member']);

            // تعليم الإشعار كمقروء
            $notification->markAsRead();

            return redirect()->route('projects.index')->with('success', 'You joined the team successfully!');
        }

        return back()->with('error', 'Invalid invitation');
    }

    // 5. رفض الدعوة (Reject)
    public function rejectInvite($notificationId)
    {
        $user = Auth::user();
        $user->notifications()->findOrFail($notificationId)->delete();

        return back()->with('info', 'Invitation rejected.');
    }
    // مغادرة الفريق (Leave Team)
    public function leave(Request $request)
    {
        $user = Auth::user();
        $team = Team::findOrFail($request->team_id);
        $project = $team->project;

        // التحقق من ديدلاين الخروج
        if ($project->leave_team_deadline && now()->gt($project->leave_team_deadline)) {
            return back()->with('error', 'Deadline to leave teams has passed. You cannot leave now.');
        }


        // 1. التحقق من الديدلاين (Deadline Check)
        if ($project->deadline && now()->greaterThan($project->deadline)) {
            return back()->withErrors(['msg' => 'Cannot leave team after the deadline!']);
        }

        // 2. لو اللي بيخرج هو الليدر
        if ($team->leader_id == $user->id) {
            // لازم يكون التيم فيه أعضاء غيره عشان يعين حد منهم
            if ($team->members->count() <= 1) {
                // لو هو لوحده في التيم، نحذف التيم كله
                $team->delete();
                return redirect()->route('projects.index')->with('success', 'Team deleted as you were the only member.');
            }

            // لازم يختار بديل
            $request->validate([
                'new_leader_id' => 'required|exists:users,id',
            ], [
                'new_leader_id.required' => 'You must assign a new leader before leaving.',
            ]);

            // التأكد إن البديل ده عضو في نفس التيم
            $isMember = $team->members()->where('user_id', $request->new_leader_id)->exists();
            if (!$isMember) {
                return back()->withErrors(['msg' => 'The selected student is not in your team!']);
            }

            // ترقية العضو الجديد ليكون هو الليدر
            $team->update(['leader_id' => $request->new_leader_id]);

            // تحديث دوره في جدول الأعضاء
            \App\Models\TeamMember::where('team_id', $team->id)
                ->where('user_id', $request->new_leader_id)
                ->update(['role' => 'leader']);
        }

        // 3. حذف الطالب المغادر من التيم
        \App\Models\TeamMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->route('projects.index')->with('success', 'You have left the team successfully.');
    }

    // 9. الإبلاغ عن عضو (Report)
    public function reportMember(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'file' => 'nullable|file|max:1048576',
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $storedPath = $file->store('reports', 'r2');
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        // تأكد إن موديل Report موجود ومستدعى فوق
        \App\Models\Report::create([
            'team_id' => $request->team_id,
            'reporter_id' => \Illuminate\Support\Facades\Auth::id(),
            'reported_user_id' => $request->reported_user_id,
            'reason' => $request->reason,
            'link' => $request->link,
            'file_path' => $path
        ]);

        return back()->with('success', 'Report sent successfully.');
    }

    // 1. طرد عضو (Remove Member) - دي اللي كانت مطلعة Error
    public function removeMember(Request $request)
    {
        $user = Auth::user();
        $team = Team::findOrFail($request->team_id);

        // أمان: لازم الليدر بس اللي يطرد
        if ($team->leader_id != $user->id) {
            return back()->withErrors(['msg' => 'Only leader can remove members.']);
        }

        // حذف العضو من الجدول
        TeamMember::where('team_id', $team->id)
            ->where('user_id', $request->user_id)
            ->delete();

        // (اختياري) حذف المهام المسندة للعضو ده في التيم عشان متبقاش معلقة
        Task::where('team_id', $team->id)->where('user_id', $request->user_id)->delete();

        return back()->with('success', 'Member removed successfully.');
    }

    // تسليم المشروع النهائي (Final Submission)
    public function submitProject(Request $request)
    {
        // 1. التعديل الأول: خلينا الملف nullable عشان يقبل يعدي من غير ملف
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'link' => 'nullable|url',
            'project_file' => 'nullable|file|mimes:pdf,zip,rar,doc,docx,png,jpg|max:1048576', // 1GB Max
            'comment' => 'nullable|string'
        ]);

        $team = Team::findOrFail($request->team_id);

        // تأكد إنه الليدر
        if ($team->leader_id != Auth::id()) {
            return back()->withErrors(['msg' => 'Only the leader can submit the project.']);
        }

        // نحتفظ بالمسار القديم لو موجود، عشان لو رفع لينك بس ما يمسحش الملف القديم بالغلط
        $path = $team->submission_path;

        // رفع الملف (لو موجود)
        if ($request->hasFile('project_file')) {
            $file = $request->file('project_file');
            $storedPath = $file->store('project_submissions', 'r2');
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        // 2. التعديل التاني: خرجنا التحديث برة الـ if عشان يحفظ اللينك حتى لو مفيش ملف
        $team->update([
            'submission_path' => $path,
            'submission_link' => $request->link, // ضفتلك السطر ده عشان يحفظ اللينك (اتأكد ان اسم العمود في الداتابيز submission_link)
            'submission_comment' => $request->comment,
            'status' => 'submitted'
        ]);

        // 🔥 الجديد: إرسال نوتيفيكيشن للدكتور (Instructor)

        $course = $team->project->course ?? null;
        $doctor = null;

        if ($course) {
            // جرب تجيب الدكتور من doctor_id أو user_id حسب اللي عندك في الداتابيز
            $doctorId = $course->doctor_id ?? $course->user_id;
            if ($doctorId) {
                $doctor = \App\Models\User::find($doctorId);
            }
        }

        if ($doctor) {
            $doctor->notify(new BatuNotification([
                'title'   => 'New Submission 📥',
                'body'    => "Team '{$team->name}' submitted their project for {$course->name}.",
                'icon'    => 'fas fa-file-import',
                'color'   => 'text-indigo-600',
                // اللينك ده يودي الدكتور لصفحة عرض التيم عشان يصحح (تأكد من اسم الراوت عندك)
                'url'     => route('staff.team.view', $team->id),
                'type'    => 'action'
            ]));
        }

        // 🔥 النوتيفيكيشن: نبلغ أعضاء التيم (ما عدا الليدر اللي بيسلم)
        foreach ($team->members as $member) {
            if ($member->user_id != Auth::id()) { // منبعتش لليدر نفسه
                $member->user->notify(new BatuNotification([
                    'title'   => 'Project Submitted ✅',
                    'body'    => "Leader has submitted the project files for {$team->project->name}.",
                    'icon'    => 'fas fa-check-circle',
                    'color'   => 'text-green-600',
                    'url'     => route('projects.show', $team->project_id),
                    'type'    => 'info'
                ]));
            }
        }

        return back()->with('success', 'Project submitted successfully! Good luck 🍀');
    }

    public function downloadProject($id)
    {
        $team = Team::findOrFail($id);

        if (!$team->submission_path) {
            return back()->withErrors(['msg' => 'File not found on server.']);
        }

        return redirect($team->submission_path);
    }
}
