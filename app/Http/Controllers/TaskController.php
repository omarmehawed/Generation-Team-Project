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
            'user_id' => 'required', // Can be single ID or array
            'team_id' => 'required|exists:teams,id',
            'deadline' => 'nullable|date',
        ]);

        // 2. Normalize user_id to an array
        $userIds = is_array($request->user_id) ? $request->user_id : [$request->user_id];

        // 3. Get Current User Member Record
        $currentUserMember = \App\Models\TeamMember::where('user_id', Auth::id())
            ->where('team_id', $request->team_id)
            ->first();

        if (!$currentUserMember || (
            !in_array($currentUserMember->role, ['leader', 'vice_leader']) &&
            !$currentUserMember->is_sub_leader
        )) {
            return back()->withErrors(['msg' => 'Unauthorized! Only Leaders, Vice Leaders, and Sub Leaders can assign tasks.']);
        }

        $team = \App\Models\Team::with('project')->find($request->team_id);
        $taskCount = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $userIds, $currentUserMember, $team, &$taskCount) {
            foreach ($userIds as $userId) {
                // Check if user exists in team
                $targetMember = \App\Models\TeamMember::where('user_id', $userId)
                    ->where('team_id', $request->team_id)
                    ->first();

                if (!$targetMember) continue;

                $canAssign = false;
                if ($currentUserMember->role == 'leader') {
                    $canAssign = true;
                } elseif ($currentUserMember->role == 'vice_leader') {
                    if (strtolower($currentUserMember->technical_role) == strtolower($targetMember->technical_role)) {
                        $canAssign = true;
                    }
                } elseif ($currentUserMember->is_sub_leader) {
                    // Sub-leader can only assign to members in their own team_number
                    if (
                        $targetMember->team_number == $currentUserMember->team_number &&
                        !$targetMember->is_sub_leader &&
                        $targetMember->role === 'member'
                    ) {
                        $canAssign = true;
                    }
                }

                if (!$canAssign) continue;

                // Create Task
                $task = \App\Models\Task::create([
                    'title' => $request->title,
                    'user_id' => $userId,
                    'team_id' => $request->team_id,
                    'technical_role' => $targetMember->technical_role, // <--- NEW (Save domain)
                    'deadline' => $request->deadline,
                    'status' => 'pending'
                ]);

                $taskCount++;

                // Notify User
                $assignedUser = \App\Models\User::find($userId);
                if ($assignedUser) {
                    $assignedUser->notify(new \App\Notifications\BatuNotification([
                        'title'      => '📌 New Task Assigned',
                        'message'    => Auth::user()->name . ' assigned you a task: "' . $request->title . '"',
                        'icon'       => 'fas fa-thumbtack',
                        'color'      => 'text-orange-500',
                        'action_url' => route('final_project.dashboard', $team->id) . '#tasks-section',
                        'type'       => 'info'
                    ]));

                    // LOG ACTIVITY
                    ActivityLogger::log(
                        action: 'task_assigned',
                        description: "Assigned task '{$request->title}' to {$assignedUser->name}",
                        subject: $task,
                        teamId: $request->team_id,
                        targetUserId: $assignedUser->id,
                        properties: ['deadline' => $request->deadline]
                    );
                }
            }
        });

        if ($taskCount == 0) {
            return back()->withErrors(['msg' => 'No tasks were assigned. Check permissions or selections.']);
        }

        return back()->with('success', "Task assigned to {$taskCount} member(s) successfully!");
    }


    public function submit(Request $request, $id) // لاحظ ضفت $id هنا عشان الراوت بيبعته
    {
        // 🛠 Ensure task_id is present for validation (even if front-end is cached)
        $request->merge(['task_id' => $id]);
        // 2. التحقق من أخطاء الرفع الأساسية قبل الـ Validation (عشان نشخص المشكلة لو سيرفر Railway رافض الحجم)
        if ($request->hasFile('submission_file') && !$request->file('submission_file')->isValid()) {
            $error = $request->file('submission_file')->getErrorMessage();
            $errorCode = $request->file('submission_file')->getError();
            \Illuminate\Support\Facades\Log::error("File upload failed before validation: " . $error . " (Code: " . $errorCode . ")");
            
            if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                return back()->with('error', 'The file is too large for the server. Maximum allowed: ' . ini_get('upload_max_filesize'));
            }
            return back()->with('error', 'File upload failed: ' . $error);
        }

        // 2. التحقق الذكي (Smart Validation)
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'submission_type' => 'required|in:file,link',

            // 1GB limit = 1048576 KB
            'submission_file' => 'required_if:submission_type,file|nullable|file|mimes:pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,gif,webp,svg|max:1048576',

            'link' => 'required_if:submission_type,link|nullable|url',

            'submission_comment' => 'nullable|string|max:2000'
        ], [
            'submission_file.max' => 'The file size cannot exceed 1GB.',
            'submission_file.uploaded' => 'The file failed to upload. Ensure it is under 1GB and your server allows large uploads. (Check PHP upload_max_filesize)',
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
                $updateData['submission_file'] = null; // Clear file if link is submitted
            }

            // 5. الحفظ النهائي للتاسك (الحالة الحالية)
            $task->update($updateData);

            // 6. الحفظ في لوج التسليمات (Version History)
            \App\Models\TaskSubmission::create([
                'task_id' => $task->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'submission_type' => $request->submission_type,
                'submission_value' => $updateData['submission_value'] ?? null,
                'submission_file' => $updateData['submission_file'] ?? null,
                'submission_comment' => $request->submission_comment,
                'status' => 'pending' // Pending review
            ]);

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

        // Domain Check for Vice Leader
        if ($currentMember->role === 'vice_leader') {
            $taskOwnerMember = \App\Models\TeamMember::where('team_id', $task->team_id)
                ->where('user_id', $task->user_id)
                ->first();
            
            if (!$taskOwnerMember || strtolower($currentMember->technical_role) !== strtolower($taskOwnerMember->technical_role)) {
                return back()->with('error', 'Unauthorized: You can only approve tasks in your own domain (' . ucfirst($currentMember->technical_role) . ').');
            }
        }

        // 3. التنفيذ
        $task->update([
            'status' => 'completed',
            'graded_at' => now(),
            'graded_by' => \Illuminate\Support\Facades\Auth::id()
        ]);

        // تحديث حالة آخر تسليم في اللوج
        $latestSubmission = $task->submissions()->latest()->first();
        if ($latestSubmission) {
            $latestSubmission->update([
                'status' => 'completed',
                'reviewed_at' => now(),
                'reviewed_by' => \Illuminate\Support\Facades\Auth::id()
            ]);
        }

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
    public function reject(Request $request, $id)
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

        // Domain Check for Vice Leader
        if ($currentMember->role === 'vice_leader') {
            $taskOwnerMember = \App\Models\TeamMember::where('team_id', $task->team_id)
                ->where('user_id', $task->user_id)
                ->first();
            
            if (!$taskOwnerMember || strtolower($currentMember->technical_role) !== strtolower($taskOwnerMember->technical_role)) {
                return back()->with('error', 'Unauthorized: You can only reject tasks in your own domain (' . ucfirst($currentMember->technical_role) . ').');
            }
        }

        // 3. التحقق من المدخلات الجديدة (بالتعديل الجديد: الديدلاين مرة واحدة فقط)
        $isSecondRejection = !empty($task->rejection_feedback);

        $request->validate([
            'feedback' => 'required|string|min:5',
            'new_deadline' => $isSecondRejection ? 'nullable|date' : 'required|date|after:now',
        ]);

        // 4. التنفيذ (لو ثانـي رفض مفيش ديدلاين جديد)
        $task->update([
            'status' => 'rejected',
            'rejection_feedback' => $request->feedback,
            'new_deadline' => $isSecondRejection ? null : $request->new_deadline,
            'deadline' => $isSecondRejection ? $task->deadline : $request->new_deadline, // لو ثاني رفض نسيب الديدلاين زي ما هو
            'graded_at' => now(),
            'graded_by' => \Illuminate\Support\Facades\Auth::id()
        ]);

        // تحديث حالة آخر تسليم في اللوج
        $latestSubmission = $task->submissions()->latest()->first();
        if ($latestSubmission) {
            $latestSubmission->update([
                'status' => 'rejected',
                'feedback' => $request->feedback,
                'reviewed_at' => now(),
                'reviewed_by' => \Illuminate\Support\Facades\Auth::id()
            ]);
        }

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

    // دالة تحميل الملف (Force Download)
    public function download($id)
    {
        $task = Task::findOrFail($id);
        if (!$task->submission_file || $task->submission_type != 'file') {
            return back()->withErrors(['msg' => 'No file attached to this task.']);
        }
        return redirect($task->submission_file);
    }

    // 6. حذف المهمة بالكامل (ليدر فقط)
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        // التحقق من الصلاحيات (ليدر التيم فقط)
        $currentMember = \App\Models\TeamMember::where('team_id', $task->team_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$currentMember || $currentMember->role !== 'leader') {
            return back()->with('error', 'Unauthorized: Only the Team Leader can delete tasks. ❌');
        }

        $task->delete();
        return back()->with('success', 'Task deleted successfully! 🗑️');
    }

    // 6.5 حذف ملف التسليم فقط (ليدر فقط)
    public function deleteSubmission($id)
    {
        $task = Task::findOrFail($id);

        // التحقق من الصلاحيات (ليدر التيم فقط)
        $currentMember = \App\Models\TeamMember::where('team_id', $task->team_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$currentMember || $currentMember->role !== 'leader') {
            return back()->with('error', 'Unauthorized: Only the Team Leader can delete submissions. ❌');
        }

        // مسح الملف من الاستورج لو موجود
        if ($task->submission_file) {
            $filePath = str_replace(config('filesystems.disks.r2.url') . '/', '', $task->submission_file);
            Storage::disk('r2')->delete($filePath);
        }

        // تحديد الحالة اللي هيرجع لها
        // لو كان فيه ریجكشن فيدباك (يعني كان مرفوض قبل كدة) يرجع لـ Rejected
        // غير كدة يرجع لـ Pending
        $newStatus = $task->rejection_feedback ? 'rejected' : 'pending';

        // تصفير بيانات التسليم
        $task->update([
            'submission_type' => null,
            'submission_file' => null,
            'submission_value' => null,
            'submission_comment' => null,
            'submitted_at' => null,
            'status' => $newStatus,
            'graded_at' => null,
            'graded_by' => null,
        ]);

        return back()->with('success', 'Submission deleted! Member status reverted to: ' . ucfirst($newStatus) . ' 🔙');
    }

    // 7. الرفع بالنيابة (Upload on Behalf)
    public function uploadOnBehalf(Request $request, $id)
    {
        $request->validate([
            'submission_file' => 'required|file|mimes:pdf,zip,rar,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,gif,webp,svg|max:1048576',
            'submission_comment' => 'nullable|string|max:2000'
        ]);

        try {
            $task = Task::findOrFail($id);
            $currentUser = Auth::user();

            // Check permissions
            $currentMember = \App\Models\TeamMember::where('team_id', $task->team_id)
                ->where('user_id', $currentUser->id)
                ->first();

            if (!$currentMember || !in_array($currentMember->role, ['leader', 'vice_leader'])) {
                return back()->with('error', 'Unauthorized: Only Leader or Vice Leader can upload on behalf.');
            }

            // Domain Check for Vice Leader
            if ($currentMember->role === 'vice_leader') {
                $taskOwnerMember = \App\Models\TeamMember::where('team_id', $task->team_id)
                    ->where('user_id', $task->user_id)
                    ->first();
                
                if (!$taskOwnerMember || strtolower($currentMember->technical_role) !== strtolower($taskOwnerMember->technical_role)) {
                    return back()->with('error', 'Unauthorized: You can only upload for members in your own domain.');
                }
            }

            // Upload File
            $file = $request->file('submission_file');
            $storedPath = $file->store('submissions', 'r2');
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);

            // Update Task
            $task->update([
                'submission_type' => 'file',
                'submission_file' => $path,
                'submission_comment' => $request->submission_comment . ' (Uploaded by ' . $currentUser->name . ')',
                'status' => 'reviewing', 
                'submitted_at' => now(),
                'graded_at' => null,
                'graded_by' => null
            ]);

            // Create Log
            \App\Models\TaskSubmission::create([
                'task_id' => $task->id,
                'user_id' => $task->user_id,
                'submission_type' => 'file',
                'submission_file' => $path,
                'submission_comment' => $request->submission_comment . ' (Uploaded by Leader: ' . $currentUser->name . ')',
                'status' => 'reviewing',
                'reviewed_by' => null,
                'reviewed_at' => null
            ]);

            return back()->with('success', 'File uploaded on behalf of member successfully! ✅');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Upload on behalf failed: ' . $e->getMessage());
            return back()->with('error', 'Upload failed.');
        }
    }
    // 8. حذف جماعي لمهام مخصصة لكل الفريق (ليدر فقط)
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'technical_role' => 'required|string',
            'title' => 'required|string',
        ]);

        // 🛡️ التأكد أن المستخدم هو الليدر
        $currentMember = \App\Models\TeamMember::where('team_id', $request->team_id)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$currentMember || $currentMember->role !== 'leader') {
            return back()->with('error', 'Unauthorized: Only the Team Leader can perform bulk deletions. ❌');
        }

        // 🗑️ تنفيذ الحذف
        $deletedCount = \App\Models\Task::where('team_id', $request->team_id)
            ->where('technical_role', strtolower($request->technical_role))
            ->where('title', $request->title)
            ->delete();

        if ($deletedCount > 0) {
            return back()->with('success', "Bulk Delete Success: {$deletedCount} tasks titled '{$request->title}' were removed from the {$request->technical_role} team. 🗑️");
        }

        return back()->with('warning', "No tasks found with the title '{$request->title}' in the {$request->technical_role} category.");
    }

    // 9. تصدير تقرير الإكسل (ليدر فقط)
    public function exportReport(Request $request)
    {
        $teamId = $request->query('team_id');
        
        if (!$teamId) {
            return back()->with('error', 'Team ID is required for export.');
        }

        // 🛡️ التأكد أن المستخدم هو الليدر
        $currentMember = \App\Models\TeamMember::where('team_id', $teamId)
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$currentMember || $currentMember->role !== 'leader') {
            return back()->with('error', 'Unauthorized: Only the Team Leader can export reports. 🚫');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TaskExport($teamId), 
            'TaskStatusReport_' . date('Y-m-d') . '.xlsx'
        );
    }
}
