<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TeamMember;
use App\Notifications\BatuNotification;
use App\Services\TeamNotifier;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use App\Services\ActivityLogger;

class TaskController extends Controller
{
    protected $guarded = [];
    // 1. إضافة مهمة (ليدر فقط)
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
            'deadline' => 'nullable|date',
        ]);

        // 2. التحقق من الصلاحيات (مين بيعمل التاسك)
        $currentUserMember = \App\Models\TeamMember::where('user_id', Auth::id())
            ->where('team_id', $request->team_id)
            ->first();

        // 3. بيانات الشخص اللي هيتعمل له التاسك
        $targetMember = \App\Models\TeamMember::where('user_id', $request->user_id)
            ->where('team_id', $request->team_id)
            ->first();

        // 4. لوجيك الصلاحيات (ليدر للكل - فايس لنفس تخصصه)
        $canAssign = false;

        if ($currentUserMember) {
            if ($currentUserMember->role == 'leader') {
                $canAssign = true;
            } elseif ($currentUserMember->role == 'vice_leader') {
                // لازم يكونوا نفس التخصص (Technical Role)
                if ($targetMember && $currentUserMember->technical_role == $targetMember->technical_role) {
                    $canAssign = true;
                }
            }
        }

        if (!$canAssign) {
            return back()->withErrors(['msg' => 'Unauthorized! Leaders can assign to all. Vice leaders to their team only.']);
        }

        // 5. إنشاء التاسك (بدون creator_id عشان ميطلعش ايرور)
        $task = \App\Models\Task::create([
            'title' => $request->title,
            'user_id' => $request->user_id,
            'team_id' => $request->team_id,
            'deadline' => $request->deadline,
            'status' => 'pending'
        ]);


        // نجيب الطالب اللي اتعمله التاسك
        // 🔥 تحديد الرابط الصحيح بناءً على نوع المشروع
        $team = \App\Models\Team::with('project')->find($request->team_id);
        $projectUrl = '#'; // Default

        if ($team->project->type == 'graduation') {
            // لو مشروع تخرج، روح للداشبورد الخاصة
            $projectUrl = route('final_project.dashboard', $team->id);
        } else {
            // لو مادة عادية، روح لصفحة عرض المادة
            $projectUrl = route('projects.show', $team->project->id);
        }

        // إرسال النوتيفيكيشن للشخص المُكلَّف فقط
        $assignedUser = \App\Models\User::find($request->user_id);
        if ($assignedUser) {
            $assignedUser->notify(new \App\Notifications\BatuNotification([
                'title'      => '📌 New Task Assigned',
                'message'    => Auth::user()->name . ' assigned you a task: "' . $request->title . '"',
                'icon'       => 'fas fa-thumbtack',
                'color'      => 'text-orange-500',
                'action_url' => route('final_project.dashboard', $team->id) . '#tasks-section',
                'type'       => 'info'
            ]));

            // 🔍 LOG ACTIVITY
            ActivityLogger::log(
                action: 'task_assigned',
                description: "Assigned task '{$request->title}' to {$assignedUser->name}",
                subject: $task,
                teamId: $request->team_id,
                targetUserId: $assignedUser->id,
                properties: ['deadline' => $request->deadline]
            );
        }

        return back()->with('success', 'Task assigned successfully!');
    }


    public function submit(Request $request, $id) // لاحظ ضفت $id هنا عشان الراوت بيبعته
    {
        // 1. دمج الـ ID اللي جاي من الرابط مع الريكويست
        $request->merge(['task_id' => $id]);

        // 2. التحقق الذكي (Smart Validation)
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'submission_type' => 'required|in:file,link',

            // الشرط السحري: الملف مطلوب فقط لو النوع "file"
            'submission_file' => 'required_if:submission_type,file|nullable|file|mimes:pdf,zip,rar,doc,docx,png,jpg|max:102400',

            // الشرط السحري: اللينك مطلوب فقط لو النوع "link"
            'link' => 'required_if:submission_type,link|nullable|url',

            'submission_comment' => 'nullable|string|max:1000'
        ]);

        try {
            $task = \App\Models\Task::findOrFail($validated['task_id']);

            // التأكد من الصلاحية (إن التاسك بتاعه)
            if ($task->user_id != \Illuminate\Support\Facades\Auth::id()) {
                return back()->with('error', 'You are not assigned to this task.');
            }

            // 3. تجهيز البيانات للتحديث
            $updateData = [
                'submission_type' => $request->submission_type,
                'submission_comment' => $request->submission_comment,
                'status' => 'reviewing',     // بنغير الحالة لمراجعة
                'submitted_at' => now(),     // بنسجل وقت التسليم
                'updated_at' => now()
            ];

            // 4. معالجة النوع (ملف ولا لينك؟)
            if ($request->submission_type === 'file') {

                // رفع الملف
                if ($request->hasFile('submission_file')) {
                    // مسح الملف القديم لو موجود
                    if ($task->submission_file && \Illuminate\Support\Facades\Storage::disk('public')->exists($task->submission_file)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($task->submission_file);
                    }

                    $file = $request->file('submission_file');
                    
                    // Determine resource type based on extension
                    $storedPath = $file->store('submissions', 'r2');
                    $path = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);

                    $updateData['submission_file'] = $path;
                    $updateData['submission_value'] = null; // بنصفر اللينك عشان ده ملف
                }
            } elseif ($request->submission_type === 'link') {

                // حفظ اللينك
                $updateData['submission_value'] = $request->link;
            }

            // 5. الحفظ النهائي
            $task->update($updateData);

            // 🔍 LOG ACTIVITY
            ActivityLogger::log(
                action: 'task_submitted',
                description: "Submitted task '{$task->title}'",
                subject: $task,
                teamId: $task->team_id,
                targetUserId: null, // Self action
                properties: ['type' => $request->submission_type]
            );

            // Notify leaders when task is submitted
            TeamNotifier::notifyLeaders($task->team_id, [
                'title'      => '📤 Task Submitted for Review',
                'message'    => Auth::user()->name . ' submitted task: "' . $task->title . '" — waiting for your approval.',
                'icon'       => 'fas fa-paper-plane',
                'color'      => 'text-blue-500',
                'action_url' => (isset($team) ? route('final_project.dashboard', $task->team_id) : '#') . '#tasks-section',
                'type'       => 'info'
            ], [Auth::id()]); // exclude the submitter

            if ($task->deadline && now()->gt($task->deadline)) {
                return back()->with('warning', 'Task submitted successfully, but it is marked as LATE because the deadline has passed. 🕒');
            }

            return back()->with('success', 'Task submitted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Task not found.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Task submission failed: ' . $e->getMessage());
            return back()->with('error', 'Submission failed. Please try again.');
        }
    }
    // 3. موافقة الليدر (Approve) -> تتحول لـ مكتملة
    // ✅ الموافقة على التاسك (ليدر + فايس ليدر)
    public function approve($id)
    {
        $task = \App\Models\Task::findOrFail($id);

        // 1. نجيب بيانات العضو اللي فاتح دلوقتي في التيم ده
        $currentMember = \App\Models\TeamMember::where('team_id', $task->team_id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        // 2. التحقق: لازم يكون موجود ويكون (Leader) أو (Vice Leader)
        if (!$currentMember || !in_array($currentMember->role, ['leader', 'vice_leader'])) {
            return back()->with('error', 'Unauthorized: Only Leader or Vice Leader can approve tasks.');
        }

        // 3. التنفيذ
        $task->update([
            'status' => 'completed',
            'graded_at' => now(),
            'graded_by' => \Illuminate\Support\Facades\Auth::id() // بنسجل مين اللي قبلها
        ]);

        // Notify the task owner that their work was approved
        $taskOwner = \App\Models\User::find($task->user_id);
        if ($taskOwner) {
            $taskOwner->notify(new BatuNotification([
                'title'      => '✅ Task Approved',
                'message'    => 'Your task "' . $task->title . '" was approved by ' . Auth::user()->name . '.',
                'icon'       => 'fas fa-check-circle',
                'color'      => 'text-green-500',
                'action_url' => route('final_project.dashboard', $task->team_id) . '#tasks-section',
                'type'       => 'success'
            ]));
        }

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'task_approved',
            description: "Approved task '{$task->title}'",
            subject: $task,
            teamId: $task->team_id,
            targetUserId: $task->user_id // The student who did the task
        );

        return back()->with('success', 'Task approved successfully! ✅');
    }

    // ❌ رفض التاسك (ليدر + فايس ليدر)
    public function reject($id)
    {
        $task = \App\Models\Task::findOrFail($id);

        // 1. نجيب بيانات العضو اللي فاتح دلوقتي
        $currentMember = \App\Models\TeamMember::where('team_id', $task->team_id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        // 2. التحقق: لازم يكون موجود ويكون (Leader) أو (Vice Leader)
        if (!$currentMember || !in_array($currentMember->role, ['leader', 'vice_leader'])) {
            return back()->with('error', 'Unauthorized: Only Leader or Vice Leader can reject tasks.');
        }

        // 3. التنفيذ
        $task->update([
            'status' => 'rejected', // أو 'returned' لو عايز ترجعها للتعديل
            'graded_at' => now(),
            'graded_by' => \Illuminate\Support\Facades\Auth::id()
        ]);

        // Notify the task owner that their work was rejected
        $taskOwner = \App\Models\User::find($task->user_id);
        if ($taskOwner) {
            $taskOwner->notify(new BatuNotification([
                'title'      => '❌ Task Rejected',
                'message'    => 'Your task "' . $task->title . '" was rejected by ' . Auth::user()->name . '. Please revise and resubmit.',
                'icon'       => 'fas fa-times-circle',
                'color'      => 'text-red-500',
                'action_url' => route('final_project.dashboard', $task->team_id) . '#tasks-section',
                'type'       => 'warning'
            ]));
        }

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'task_rejected',
            description: "Rejected task '{$task->title}'",
            subject: $task,
            teamId: $task->team_id,
            targetUserId: $task->user_id
        );

        return back()->with('error', 'Task rejected ❌');
    }
    // 5. تبديل الحالة السريع (Toggle)
    public function toggle($id)
    {
        $task = Task::findOrFail($id);

        if ($task->status == 'pending') {
            $task->update(['status' => 'completed', 'submitted_at' => now()]);
        } elseif ($task->status == 'completed') {
            $task->update(['status' => 'pending']);
        }

        return back();
    }

    // 6. حذف المهمة
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return back()->with('success', 'Task deleted');
    }

    // دالة تحميل الملف (Force Download)
    public function download($id)
    {
        $task = Task::findOrFail($id);

        // التأكد إن فيه ملف أصلاً
        if (!$task->submission_value || $task->submission_type != 'file') {
            return back()->withErrors(['msg' => 'No file attached to this task.']);
        }

        // التأكد إن الملف موجود (طالما هو رابط Cloudinary يعتبر موجود)
        // أمر التحميل المباشر للرابط الخارجي
        return redirect($task->submission_value);
    }
}
