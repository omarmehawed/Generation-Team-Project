<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ProjectFund;
use Illuminate\Support\Facades\DB;
use App\Notifications\BatuNotification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use App\Services\ActivityLogger;

class FinalProjectController extends Controller
{
    // 1. Start Project Page (Single Team Logic)
    public function start()
    {
        $user = Auth::user();

        // 1. Get the Graduation Project
        $finalProject = Project::where('type', 'graduation')->first();
        if (!$finalProject) {
            return view('final_project.error', ['message' => 'System not initialized. Please contact admin.']);
        }

        // 2. Check if the SINGLE Team exists
        $singleTeam = Team::where('project_id', $finalProject->id)->first();

        if (!$singleTeam) {
             return view('final_project.init_team', ['project' => $finalProject]);
        }

        // 3. Check User's Membership
        $membership = TeamMember::where('user_id', $user->id)
            ->where('team_id', $singleTeam->id)
            ->first();

        // A. Not a member? -> Show "Join Team" Landing Page
        if (!$membership) {
            return view('final_project.join_landing', [
                'team' => $singleTeam,
                'project' => $finalProject
            ]);
        }

        // B. Pending? -> Show "Waiting for Approval" Page
        if ($membership->status == 'pending') {
            return view('final_project.waiting_approval', ['team' => $singleTeam]);
        }

        // C. Rejected? -> Show "Access Denied" Page
        if ($membership->status == 'rejected') {
            return view('final_project.rejected', ['team' => $singleTeam]);
        }

        // D. Active? -> Go to Dashboard
        return $this->dashboard($singleTeam->id);
    }

    // 2. Join Team
    public function joinTeam(Request $request)
    {
        $user = Auth::user();
        
        $team = Team::findOrFail($request->team_id);
        
        $exists = TeamMember::where('team_id', $team->id)->where('user_id', $user->id)->exists();
        if ($exists) {
            return back()->with('error', 'You have already sent a request.');
        }

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'member',
            'sub_team' => 'general',
            'status' => 'pending'
        ]);

        $leader = User::find($team->leader_id);
        if ($leader) {
            $leader->notify(new BatuNotification([
                'title' => 'New Join Request 👤',
                'body' => $user->name . ' wants to join the team.',
                'icon' => 'fas fa-user-clock',
                'color' => 'text-yellow-500',
                'url' => route('final_project.dashboard', $team->id),
                'type' => 'info'
            ]));
        }

        return redirect()->route('final_project.start')->with('success', 'Request sent! Waiting for leader approval.');
    }

    // 3. Approve Member
    public function approveMember($member_id)
    {
        $membership = TeamMember::findOrFail($member_id);
        $team = Team::findOrFail($membership->team_id);

        if (Auth::id() != $team->leader_id) {
            abort(403, 'Unauthorized');
        }

        $membership->update(['status' => 'active', 'joined_at' => now()]);
        
        $user = User::find($membership->user_id);
        $user->notify(new BatuNotification([
            'title' => 'Welcome Aboard! 🚀',
            'body' => 'Your request to join ' . $team->name . ' has been approved.',
            'icon' => 'fas fa-check-circle',
            'color' => 'text-green-500',
            'url' => route('final_project.dashboard', $team->id),
            'type' => 'success'
        ]));

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'member_approved',
            description: "Approved join request for {$user->name}",
            subject: $team,
            teamId: $team->id,
            targetUserId: $user->id
        );

        return back()->with('success', 'Member approved successfully.');
    }


    // 4. Reject Member
    public function rejectMember($member_id)
    {
        $membership = TeamMember::findOrFail($member_id);
        $team = Team::findOrFail($membership->team_id);

        if (Auth::id() != $team->leader_id) {
            abort(403, 'Unauthorized');
        }

        $membership->delete();

        $user = User::find($membership->user_id);
        $user->notify(new BatuNotification([
            'title' => 'Request Declined ❌',
            'body' => 'Your request to join ' . $team->name . ' was declined.',
            'icon' => 'fas fa-times-circle',
            'color' => 'text-red-500',
            'url' => route('final_project.start'),
            'type' => 'alert'
        ]));

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'member_rejected',
            description: "Declined join request from {$user->name}",
            subject: $team,
            teamId: $team->id,
            targetUserId: $user->id
        );

        return back()->with('success', 'Member request rejected.');
    }
    
    // 5. Store Initial Team
    public function storeTeam(Request $request)
    {
         $gradProject = Project::where('type', 'graduation')->first();
         if(Team::where('project_id', $gradProject->id)->exists()){
             return back()->with('error', 'A team already exists for this project.');
         }
         
         $request->merge(['project_id' => $gradProject->id]);

         $request->validate([
            'name' => 'required|string|max:255',
         ]);
        
         $user = Auth::user();

        $team = Team::create([
            'name' => $request->name,
            'code' => strtoupper(Str::random(6)),
            'project_id' => $gradProject->id,
            'leader_id' => $user->id,
            'status' => 'forming',
            'project_phase' => 'proposal',
            'proposal_status' => 'pending',
            'logo' => null
        ]);
        
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'leader',
            'sub_team' => 'general',
            'status' => 'active'
        ]);

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'team_created',
            description: "Created new team '{$team->name}'",
            subject: $team,
            teamId: $team->id,
            targetUserId: null // Self
        );

        return redirect()->route('final_project.dashboard', $team->id);
    }
    
    // ... Old Update Logo Logic (kept for compatibility) ...
    public function updateLogo(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'team_logo_base64' => 'required',
        ]);

        $team = Team::findOrFail($request->team_id);

        if ($team->leader_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $base64Image = $request->input('team_logo_base64');

        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]);
            $data = base64_decode($data);

            if ($data !== false) {
                if ($team->logo && Storage::disk('public')->exists($team->logo)) {
                    Storage::disk('public')->delete($team->logo);
                }

                $fileName = 'team_' . $team->id . '_' . time() . '.' . $type;
                $path = 'team_logos/' . $fileName;

                Storage::disk('public')->put($path, $data);
                $team->update(['logo' => $path]);
            }
        }

        return back()->with('success', 'Team badge updated successfully!');
    }

    public function getTeamLogo($id)
    {
        $team = Team::findOrFail($id);

        if (!$team->logo) {
            abort(404);
        }

        return redirect($team->logo);
    }
    public function dashboard($teamId)
    {
        $team = Team::with(['members.user', 'expenses', 'gallery', 'tasks.user'])->findOrFail($teamId);

        // Check if user is a member (AND is active)
        $myMemberRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myMemberRecord || $myMemberRecord->status != 'active') {
             return redirect()->route('final_project.start')->with('error', 'Access Denied or Pending Approval.');
        }

        $project = Project::find($team->project_id);

        // 1. Funding Logic (Active Fund)
        $activeFund = ProjectFund::where('team_id', $team->id)
            ->with('contributions.user')
            ->latest()
            ->first();

        // 2. Funding History
        $fundsHistory = ProjectFund::where('team_id', $team->id)
            ->with('contributions.user')
            ->where('id', '!=', $activeFund?->id) 
            ->latest()
            ->get();

        // 3. Debts Calculation
        $membersDebts = [];
        foreach ($team->members as $member) {
            $debt = \App\Models\FundContribution::where('user_id', $member->user_id)
                ->whereHas('fund', function ($q) use ($team, $activeFund) {
                    $q->where('team_id', $team->id);
                    if ($activeFund) $q->where('id', '!=', $activeFund->id); 
                })
                ->where('status', '!=', 'paid') 
                ->get()
                ->sum(function ($contrib) {
                    return $contrib->fund->amount_per_member; 
                });

            $membersDebts[$member->user_id] = $debt;
        }

        // Determine Role
        $myRole   = $myMemberRecord->role ?? 'member';
        
        // 4. Pending Members (For Leader's Eyes Only)
        $pendingMembers = [];
        if($myRole == 'leader'){
            $pendingMembers = TeamMember::where('team_id', $team->id)
                                ->where('status', 'pending')
                                ->with('user')
                                ->get();
        }

        return view('final_project.dashboard', compact(
            'team',
            'project',
            'activeFund',
            'fundsHistory',
            'membersDebts',
            'myRole',
            'pendingMembers' // Passed to view
        ));
    }

    // 5. Leave Team
    public function leaveTeam(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ]);

        $user = Auth::user();
        $team = Team::findOrFail($request->team_id);

        // هات سجل العضوية عشان نعرف الدور
        $memberRecord = TeamMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$memberRecord) {
            return back()->with('error', 'You are not a member of this team.');
        }

        // --- منطق الليدر ---
        if ($memberRecord->role == 'leader') {
            // 1. لو هو لوحده في التيم (مفيش أعضاء غيره) -> امسح التيم كله
            if ($team->members()->count() <= 1) {
                $team->delete();
                return redirect()->route('final_project.start')->with('success', 'Team deleted because you were the only member.');
            }

            // 2. لو فيه أعضاء تانيين -> لازم يختار بديل
            $request->validate([
                'new_leader_id' => 'required|exists:users,id',
            ], [
                'new_leader_id.required' => 'Team Leader cannot leave without assigning a new leader.',
            ]);

            // اتأكد إن البديل ده معانا في التيم أصلاً
            $isMember = $team->members()->where('user_id', $request->new_leader_id)->exists();
            if (!$isMember) {
                return back()->with('error', 'The selected member is not in this team.');
            }

            // أداء عملية التبديل
            // أ. تحديث الليدر في جدول التيم الأساسي
            $team->update(['leader_id' => $request->new_leader_id]);

            // ب. تحديث دور العضو الجديد ليصبح Leader في جدول الأعضاء
            TeamMember::where('team_id', $team->id)
                ->where('user_id', $request->new_leader_id)
                ->update(['role' => 'leader']);
        }

        // --- الخروج الطبيعي ---
        // امسح سجل العضو اللي بيخرج (سواء كان ليدر سابق أو عضو عادي)
        $memberRecord->delete();

        return redirect()->route('final_project.start')->with('success', 'You have left the team successfully.');
    }

    // 6. Invite Member (Placeholder)
    // ==========================================
    // 6. Invite Member (Direct Add by Email)
    // ==========================================
    public function inviteMember(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'email'   => 'required|email|exists:users,email',
        ]);

        $team = Team::findOrFail($request->team_id);

        // 1. التأكد إن اللي بيعمل دعوة هو الليدر (للأمان)
        if (Auth::id() != $team->leader_id) {
            return back()->with('error', 'Only the Team Leader can invite members.');
        }

        // 2. نجيب اليوزر اللي عايزين نضيفه
        $userToAdd = User::where('email', $request->email)->first();

        // 3. نتأكد إنه مش في التيم ده أصلاً
        if ($team->members()->where('user_id', $userToAdd->id)->exists()) {
            return back()->with('error', 'This user is already in your team.');
        }

        // 4. نتأكد إنه مش في أي تيم تاني لنفس المشروع (عشان ميكونش بوشين)
        $alreadyInAnotherTeam = TeamMember::where('user_id', $userToAdd->id)
            ->whereHas('team', function ($q) use ($team) {
                $q->where('project_id', $team->project_id);
            })->exists();

        if ($alreadyInAnotherTeam) {
            return back()->with('error', 'This user is already a member of another team.');
        }

        // 5. نتأكد إن التيم مش كامل (100 عضو مثلاً)
        if ($team->members()->count() >= 100) {
            return back()->with('error', 'Team is full.');
        }

        // 6. إضافة العضو (Direct Add)
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $userToAdd->id,
            'role'    => 'member',
            'sub_team' => 'general'
        ]);

        // 7. إرسال النوتيفيكيشن
        $userToAdd->notify(new BatuNotification([
            'title'   => 'Welcome Aboard! 🚀',
            'body'    => 'You have been added to Team: ' . $team->name,
            'icon'    => 'fas fa-user-plus',
            'color'   => 'text-green-500',
            'url'     => route('final_project.dashboard', $team->id),
            'type'    => 'info'
        ]));

        return back()->with('success', 'Member added successfully!');
    }

    // ==========================================
    // 7. Remove Member
    // ==========================================
    public function removeMember(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $team = Team::findOrFail($request->team_id);

        // 1. أمان: الليدر بس اللي يمسح
        if (Auth::id() != $team->leader_id) {
            return back()->with('error', 'Only the Team Leader can remove members.');
        }

        // 2. ممنوع الليدر يمسح نفسه من الزرار ده (يستخدم Leave Team)
        if ($request->user_id == $team->leader_id) {
            return back()->with('error', 'You cannot remove yourself. Use "Leave Team" instead.');
        }

        // 3. المسح
        $memberRecord = TeamMember::where('team_id', $team->id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($memberRecord) {
            $memberRecord->delete();

            // 4. إرسال النوتيفيكيشن للشخص المطرود
            $removedUser = User::find($request->user_id);
            if ($removedUser) {
                $removedUser->notify(new BatuNotification([
                    'title'   => 'Team Update ⚠️',
                    'body'    => 'You have been removed from Team: ' . $team->name,
                    'icon'    => 'fas fa-user-times',
                    'color'   => 'text-red-500',
                    'url'     => route('final_project.start'), // نرجعه للصفحة الرئيسية
                    'type'    => 'alert'
                ]));
            }

            return back()->with('success', 'Member removed successfully.');
        }

        return back()->with('error', 'Member not found in this team.');
    }

    // 8. Report Member (Placeholder)
    public function reportMember(Request $request)
    {
        $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'complaint' => 'required|string|min:10',
        ]);

        $user = Auth::user();
        // هات تيم الطالب
        $team = Team::where('leader_id', $user->id)
            ->orWhereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();

        DB::table('member_reports')->insert([
            'team_id' => $team->id,
            'reporter_id' => $user->id,
            'reported_user_id' => $request->reported_user_id,
            'complaint' => $request->complaint,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Report submitted successfully.');
    }

    // 9. Create Team View (Unused if using modal, but kept for reference)
    public function createTeamView($projectId)
    {
        $project = Project::findOrFail($projectId);
        return view('final_project.dashboard', compact('project'));
    }

    // ==========================================
    // 9. تقديم مقترح المشروع (Submit Proposal)
    // ==========================================
    public function submitProposal(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'proposal_title' => 'required|string|max:255',
            'proposal_description' => 'required|string',
            // خليناها 10240 كيلو بايت (يعني 10 ميجا) عشان ملفك 5.8 ميجا
            'proposal_file' => 'required|file|mimes:pdf,doc,docx,pptx|max:10240',
        ]);

        $team = Team::findOrFail($request->team_id);

        // التأكد إن المستخدم هو الليدر
        if (Auth::id() != $team->leader_id) {
            return back()->with('error', 'Only the Leader can submit the proposal.');
        }

        // 2. رفع الملف (Upload)
        $filePath = null;
        if ($request->hasFile('proposal_file')) {
            // هيحفظ الملف ويرجع المسار بتاعه في المتغير $filePath
            $filePath = Cloudinary::upload($request->file('proposal_file')->getRealPath(), ['folder' => 'proposals'])->getSecurePath();
        }

        // 3. التحديث (Update) - الحفظ في الداتا بيز
        $team->update([
            'proposal_title' => $request->proposal_title,
            'proposal_description' => $request->proposal_description,

            // ✅ بنحفظ المتغير اللي شايل المسار (مش جملة الـ validation)
            'proposal_file' => $filePath,

            'proposal_status' => 'pending',
            'project_phase' => 'proposal_submitted'
        ]);

        // ... جوه دالة submitProposal في الآخر

        // 1. نجيب الدكتور المشرف على التيم ده (أو الدكتور أسامة لو مفيش TA)

        $doctor = User::find($team->ta_id) ?? User::where('role', 'doctor')->first();

        if ($doctor) {
            $doctor->notify(new BatuNotification([
                'title'       => 'New Proposal 📄',
                'body'        => 'Team ' . $team->name . ' submitted a new proposal.',
                'icon'        => 'fas fa-file-signature',
                'color'       => 'text-purple-500',
                'url'         => route('staff.proposals'), // الصفحة اللي الدكتور بيشوف فيها البروبوزال
                'type'        => 'action',
                'action_type' => 'proposal_review',
                'model_id'    => $team->id
            ]));
        }

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'proposal_submitted',
            description: "Submitted project proposal '{$request->proposal_title}'",
            subject: $team,
            teamId: $team->id,
            targetUserId: null,
            properties: ['file' => $filePath]
        );

        return back()->with('success', 'Proposal submitted successfully! Waiting for approval.');
    }

    //  دالة جديدة لعرض الملف (View File Route)
    public function viewProposalFile($team_id)
    {
        $team = Team::findOrFail($team_id);


        if (!$team->proposal_file) {
            abort(404);
        }


        // لو رافعه عادي استخدم: storage_path('app/' . $team->proposal_file)


        if (!$team->proposal_file) {
            abort(404, 'No proposal file found');
        }

        return redirect($team->proposal_file);
    }

    // 10. تعديل أدوار وتخصصات العضو (Updated)
    public function updateMemberStatus(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,vice_leader,leader_b', // الدور الإداري
            'technical_role' => 'required|in:general,software,hardware', // التخصص التقني
            'extra_role' => 'nullable|in:none,presentation,reports,marketing' // المسؤولية الإضافية
        ]);

        $leader = Auth::user();
        $team = Team::findOrFail($request->team_id);
        $team = Team::findOrFail($request->team_id);

        // 1. Get Current User ID once
        $currentUserId = Auth::id();

        // 2. Check Roles
        $isLeader = $team->leader_id === $currentUserId;

        // تأكدنا هنا من استخدام المتغير بدلاً من استدعاء الدالة مرتين
        $isViceLeader = $team->members()
            ->where('user_id', $currentUserId)
            ->where('role', 'vice_leader')
            ->exists();

        // 3. Final Authorization Check
        if (! $isLeader && ! $isViceLeader) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->user_id == $leader->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $updateData = [
            'role' => $request->role,
            'technical_role' => $request->technical_role,
            'extra_role' => ($request->extra_role == 'none') ? null : $request->extra_role
        ];

        if ($request->role === 'leader_b') {
            $updateData['is_group_b'] = true;
            $updateData['is_group_a'] = false;
        }

        TeamMember::where('team_id', $request->team_id)
            ->where('user_id', $request->user_id)
            ->update($updateData);

        return back()->with('success', 'Member roles updated successfully ✨');
    }



    // ==========================================
    // 10b. Assign/Toggle Group A/B (For members)
    // ==========================================
    public function toggleGroup(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
            'group'   => 'required|in:A,B',
        ]);

        $team = Team::findOrFail($request->team_id);
        
        $myMemberRecord = TeamMember::where('team_id', $team->id)->where('user_id', Auth::id())->first();
        $isLeaderManaging = $myMemberRecord && $myMemberRecord->role == 'leader';
        $isViceManaging = $myMemberRecord && $myMemberRecord->role == 'vice_leader';

        if (!$isLeaderManaging && !$isViceManaging && Auth::id() != $request->user_id) {
            return back()->with('error', 'Unauthorized to assign this member.');
        }

        $memberRecord = TeamMember::where('team_id', $team->id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$memberRecord) {
            return back()->with('error', 'Member is not in this team.');
        }

        // Leader locks
        if ($memberRecord->role === 'leader' && $request->group === 'B') {
            return back()->with('error', 'The Main Leader must be in Group A.');
        }
        if ($memberRecord->role === 'leader' && $request->group === 'A' && $memberRecord->is_group_a) {
            return back()->with('error', 'The Main Leader cannot leave Group A.');
        }
        if ($memberRecord->role === 'leader_b' && $request->group === 'A') {
            return back()->with('error', 'Leader Group B must be in Group B.');
        }
        if ($memberRecord->role === 'leader_b' && $request->group === 'B' && $memberRecord->is_group_b) {
            return back()->with('error', 'Leader Group B cannot leave Group B.');
        }

        $groupField = 'is_group_' . strtolower($request->group);
        $isCurrentlyInGroup = $memberRecord->$groupField;

        // If adding to group, check limit
        if (!$isCurrentlyInGroup) {
            $currentCount = TeamMember::where('team_id', $team->id)
                ->where($groupField, true)
                ->count();

            if ($currentCount >= 30) {
                return back()->with('error', 'Group ' . $request->group . ' is already full (Max 30 members).');
            }
            $memberRecord->update([$groupField => true]);
            
            $msg = Auth::id() == $request->user_id ? 'You successfully joined Group ' . $request->group . '!' : 'Assigned member to Group ' . $request->group . '!';
            return back()->with('success', $msg);
        } else {
            $memberRecord->update([$groupField => false]);
            $msg = Auth::id() == $request->user_id ? 'You left Group ' . $request->group . '!' : 'Removed member from Group ' . $request->group . '!';
            return back()->with('success', $msg);
        }
    }

    // ==========================================
    // 11. إضافة مصروفات
    // ==========================================
    public function storeExpense(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'item' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255', // اسم المحل
            'amount' => 'required|numeric|min:1',
            'receipt' => 'nullable|image|max:5120' // صورة الوصل (اختياري) بحد أقصى 5MB
        ]);

        $team = Team::findOrFail($request->team_id);

        // حماية: التأكد إن الطالب في التيم
        if (!$team->members->contains('user_id', Auth::id())) {
            return back()->with('error', 'Unauthorized.');
        }

        // رفع صورة الوصل لو موجودة
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = Cloudinary::upload($request->file('receipt')->getRealPath(), ['folder' => 'receipts'])->getSecurePath();
        }

        // الحفظ في الداتابيز
        DB::table('project_expenses')->insert([
            'team_id' => $request->team_id,
            'user_id' => Auth::id(),
            'item' => $request->item,
            'shop_name' => $request->shop_name, // حفظنا اسم المحل
            'amount' => $request->amount,
            'receipt_path' => $receiptPath, // حفظنا مسار الصورة
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // storeExpense بعد الحفظ

        // نجيب كل أعضاء التيم ما عدا
        $members = $team->members()->where('user_id', '!=', Auth::id())->get();

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'expense_added',
            description: "Added expense: {$request->item} ({$request->amount} EGP)",
            subject: $team,
            teamId: $team->id,
            targetUserId: null,
            properties: ['amount' => $request->amount, 'shop' => $request->shop_name]
        );

        return back()->with('success', 'Expense recorded successfully! 💸');
    }
    // ==========================================
    // 12. إنشاء طلب تمويل (Create Fund Request)
    // ==========================================
    public function storeFund(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'title' => 'required|string|max:255',
            'amount_per_member' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
        ]);

        $team = Team::with('members')->findOrFail($request->team_id);

        // 1. إنشاء الطلب الأساسي
        $fund = ProjectFund::create([
            'team_id' => $team->id,
            'title' => $request->title,
            'amount_per_member' => $request->amount_per_member,
            'deadline' => $request->deadline,
        ]);

        // 2. إنشاء سجل لكل عضو في التيم (بما فيهم الليدر)
        foreach ($team->members as $member) {
            \App\Models\FundContribution::create([
                'fund_id' => $fund->id,
                'user_id' => $member->user_id,
                'status' => 'pending'
            ]);
        }

        return back()->with('success', 'Funding round started! Members notified to pay.');
    }


    // ==========================================
    // 13. [NEW] Submit Payment (Member Side)
    // ==========================================
    public function submitPayment(Request $request)
    {
        $request->validate([
            'contribution_id' => 'required|exists:fund_contributions,id',
            'payment_method' => 'required|in:cash,transfer',
        ]);

        $contrib = \App\Models\FundContribution::findOrFail($request->contribution_id);

        // Authorization: Ensure the user owns this contribution
        if ($contrib->user_id != Auth::id()) {
             return back()->with('error', 'Unauthorized action.');
        }

        // Specific Validations based on Method
        if ($request->payment_method == 'transfer') {
            $request->validate([
                'amount_transferred' => 'required|numeric',
                'from_number' => 'required|string',
                'transaction_date' => 'required|date',
                'transaction_time' => 'required',
                'proof_image' => 'required|image|max:5120'
            ]);
        } else {
            $request->validate([
                'notes' => 'required|string|min:5',
            ]);
        }

        // Update Logic
        $updateData = [
            'payment_method' => $request->payment_method,
            'status' => 'pending_approval', 
            'rejection_reason' => null, // Clear any previous rejection
        ];

        if ($request->payment_method == 'transfer') {
            // Upload Proof
            if ($request->hasFile('proof_image')) {
                $path = Cloudinary::upload($request->file('proof_image')->getRealPath(), ['folder' => 'payment_proofs'])->getSecurePath();
                $updateData['payment_proof'] = $path;
            }
            $updateData['amount'] = $request->amount_transferred; // Assuming column is 'amount' (it is in schema)
            $updateData['from_number'] = $request->from_number;
            $updateData['transaction_date'] = $request->transaction_date;
            $updateData['transaction_time'] = $request->transaction_time;
        } else {
             $updateData['notes'] = $request->notes;
        }

        $contrib->update($updateData);

        // Notify Leader
        $team = Team::find($contrib->fund->team_id);
        $leader = User::find($team->leader_id);
        
        // Notify Leader
        if ($leader) {
             $leader->notify(new BatuNotification([
                'title' => 'Payment Submitted 💸',
                'body' => Auth::user()->name . ' submitted a payment request.',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'text-yellow-500',
                'url' => route('final_project.dashboard', $team->id),
                'type' => 'info'
            ]));
        }

        return back()->with('success', 'Payment submitted! Waiting for leader approval.');
    }

    // ==========================================
    // 14. [NEW] Review Payment (Leader Side)
    // ==========================================
    public function reviewPayment(Request $request)
    {
        $request->validate([
            'contribution_id' => 'required|exists:fund_contributions,id',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject'
        ]);

        $contrib = \App\Models\FundContribution::findOrFail($request->contribution_id);
        $team = Team::find($contrib->fund->team_id);

        // Authorization: Leader Only
        if (Auth::id() != $team->leader_id) {
             return back()->with('error', 'Unauthorized. Only Team Leader can review.');
        }

        $user = User::find($contrib->user_id);

        if ($request->action == 'approve') {
            $contrib->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Notify Member (Success)
            $user->notify(new BatuNotification([
                'title' => 'Payment Approved ✅',
                'body' => 'Your payment for ' . $contrib->fund->title . ' has been confirmed.',
                'icon' => 'fas fa-check-circle',
                'color' => 'text-green-500',
                'url' => route('final_project.dashboard', $team->id),
                'type' => 'success'
            ]));

            return back()->with('success', 'Payment approved successfully.');

        } else {
            $contrib->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);

            // Notify Member (Rejected)
            $user->notify(new BatuNotification([
                'title' => 'Payment Rejected ❌',
                'body' => 'Reason: ' . $request->rejection_reason,
                'icon' => 'fas fa-times-circle',
                'color' => 'text-red-500',
                'url' => route('final_project.dashboard', $team->id),
                'type' => 'alert'
            ]));

            return back()->with('success', 'Payment rejected.');
        }
    }

    // ==========================================
    // 13. تسجيل دفع عضو (Mark as Paid) -> DEPRECATED/UPDATED
    // ==========================================
    /*
    public function markPaid(Request $request)
    {
        // ... (Old logic commented out or removed)
    }
    */




    // دالة لعرض الملفات المحمية (صور الإيصالات والتحويلات)
    public function viewAttachment($path)
    {
        // Use Storage facade for secure serving and correct headers
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
    // ==========================================
    // 14. رفع تقرير أسبوعي (MCR: Modified for Date/Time)
    // ==========================================
    public function storeReport(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'week_number' => 'required|integer|min:1|max:30',
            'report_date' => 'required|date',
            'achievements' => 'required|string',
            'plans' => 'required|string',
            'report_file' => 'nullable|file|max:20480' // 20MB limit
        ]);

        $user = Auth::user();

        // Check permissions (Leader or Report Manager)
        $member = TeamMember::where('team_id', $request->team_id)
            ->where('user_id', $user->id)
            ->first();

        // Allow if user is leader OR has report role
        $isLeader = Team::where('id', $request->team_id)->where('leader_id', $user->id)->exists();

        if (!$isLeader && ($member->extra_role ?? '') != 'reports') {
            return back()->with('error', 'Only the Leader or Report Manager can submit weekly reports.');
        }

        // Handle File Upload
        $filePath = null;
        if ($request->hasFile('report_file')) {
            $filePath = Cloudinary::upload($request->file('report_file')->getRealPath(), ['folder' => 'weekly_reports'])->getSecurePath();
        }

        // Insert into Database
        DB::table('weekly_reports')->insert([
            'team_id' => $request->team_id,
            'user_id' => $user->id,
            'week_number' => $request->week_number,
            'report_date' => $request->report_date,
            'achievements' => $request->achievements,
            'plans' => $request->plans,
            'challenges' => $request->challenges,
            'file_path' => $filePath,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ... جوه دالة storeReport في الآخر

        // نجيب المشرف (TA) بتاع التيم
        $ta = User::find(Team::find($request->team_id)->ta_id);

        if ($ta) {
            $ta->notify(new BatuNotification([
                'title'   => 'Weekly Report 📊',
                'body'    => 'Week ' . $request->week_number . ' report submitted.',
                'icon'    => 'fas fa-calendar-check',
                'color'   => 'text-blue-500',
                'url'     => route('staff.team.manage', $request->team_id), // لينك صفحة التيم عند المعيد
                'type'    => 'info'
            ]));
        }

        return back()->with('success', 'Weekly Report submitted successfully 📝');
    }
    // ==========================================
    // 15. طلب اجتماع مع المشرف (Supervision)
    // ==========================================
    public function requestSupervisionMeeting(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'topic' => 'required|string|max:255',
            'description' => 'required|string', // 🔥 الخانة الجديدة
            'meeting_date' => 'required|date|after:today',
            'mode' => 'required|in:online,offline',
        ]);

        $team = Team::findOrFail($request->team_id);
        if (Auth::id() != $team->leader_id) return back()->with('error', 'Only Leader can book.');

        \App\Models\Meeting::create([
            'team_id' => $team->id,
            'topic' => $request->topic,
            'description' => $request->description, // حفظ التفاصيل
            'meeting_date' => $request->meeting_date,
            'mode' => $request->mode,
            'type' => 'supervision',
            'status' => 'pending'
        ]);

        // ... جوه دالة requestSupervisionMeeting

        // نجيب المشرف (TA)
        $ta = User::find($team->ta_id);

        if ($ta) {
            $ta->notify(new BatuNotification([
                'title'   => 'Meeting Request 🤝',
                'body'    => 'Team ' . $team->name . ' requested a supervision meeting.',
                'icon'    => 'fas fa-handshake',
                'color'   => 'text-green-600',
                'url'     => route('staff.team.manage', $team->id), // يروح لصفحة إدارة التيم
                'type'    => 'action', // ممكن تخليها action عشان يوافق أو يرفض
                'action_type' => 'meeting_request',
                'model_id' => $team->id
            ]));
        }

        return back()->with('success', 'Request sent to supervisor 📅');
    }

    // ==========================================
    // 16. إنشاء اجتماع داخلي (محدث بالمكان)
    // ==========================================
    public function storeInternalMeeting(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'topic' => 'required|string',
            'meeting_date' => 'required|date',
            'mode' => 'required|in:online,offline',
            // التحقق الشرطي
            'meeting_link' => 'nullable|required_if:mode,online|url',
            'location_type' => 'nullable|required_if:mode,offline|in:college,other',
            'custom_location' => 'nullable|required_if:location_type,other|string'
        ]);

        // تحديد المكان بناءً على الاختيار
        $finalLocation = null;
        if ($request->mode == 'offline') {
            $finalLocation = ($request->location_type == 'college') ? 'College Campus' : $request->custom_location;
        }

        $meeting = \App\Models\Meeting::create([
            'team_id' => $request->team_id,
            'topic' => $request->topic,
            'meeting_date' => $request->meeting_date,
            'mode' => $request->mode,
            'meeting_link' => $request->mode == 'online' ? $request->meeting_link : null,
            'location' => $finalLocation, // حفظنا المكان
            'type' => 'internal',
            'status' => 'confirmed'
        ]);

        // سجل الحضور
        $team = Team::find($request->team_id);
        foreach ($team->members as $member) {
            \App\Models\MeetingAttendance::create([
                'meeting_id' => $meeting->id,
                'user_id' => $member->user_id,
                'is_present' => false
            ]);
        }
        // ... جوه دالة storeInternalMeeting بعد الحفظ

        // نجيب التيم عشان نلف على الأعضاء
        $team = Team::find($request->team_id);

        // نلف على كل الأعضاء ما عدا الليدر (اللي هو أنا)
        $members = $team->members()->where('user_id', '!=', Auth::id())->get();

        foreach ($members as $member) {
            $userToNotify = User::find($member->user_id);

            if ($userToNotify) {
                $userToNotify->notify(new BatuNotification([
                    'title'   => 'New Team Meeting 📅',
                    'body'    => 'Topic: ' . $request->topic . ' on ' . $request->meeting_date,
                    'icon'    => 'fas fa-users', // أيقونة تعبر عن الاجتماع
                    'color'   => 'text-indigo-500',
                    'url'     => route('final_project.dashboard', $team->id),
                    'type'    => 'info'
                ]));
            }
        }

        return back()->with('success', 'Internal meeting scheduled successfully.');
    }

    // ==========================================
    // 17. تسجيل الحضور (Attendance)
    // ==========================================
    public function markAttendance(Request $request)
    {
        $request->validate(['meeting_id' => 'required|exists:meetings,id']);

        $meeting = \App\Models\Meeting::findOrFail($request->meeting_id);

        // 1. تصفير الحضور
        \App\Models\MeetingAttendance::where('meeting_id', $meeting->id)->update(['is_present' => false]);

        // 2. تسجيل الحضور للي اخترناهم
        if ($request->has('attendees')) {
            \App\Models\MeetingAttendance::where('meeting_id', $meeting->id)
                ->whereIn('user_id', $request->attendees)
                ->update(['is_present' => true]);
        }

        $meeting->update(['status' => 'completed']);

        return back()->with('success', 'Attendance saved ✅');
    }

    // ==========================================
    // 18. رفع صور للمشروع (Project Gallery)
    // ==========================================
    public function uploadGallery(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'caption' => 'required|string|max:100',
            'category' => 'required|in:prototype,diagram,software,event,other',
            'type' => 'required|in:image,video',

            // الصورة مطلوبة فقط لو النوع كان image
            'image' => 'required_if:type,image|nullable|image|max:10240',

            // اللينك مطلوب فقط لو النوع كان video
            'video_link' => 'required_if:type,video|nullable|url', // Max 5MB
        ]);

        // رفع الصورة
        $filePath = null;
        if ($request->type == 'image' && $request->hasFile('image')) {
            $filePath = Cloudinary::upload($request->file('image')->getRealPath(), ['folder' => 'gallery'])->getSecurePath();
        }

        DB::table('project_galleries')->insert([
            'team_id' => $request->team_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'file_path' => $filePath,
            'video_link' => $request->video_link,
            'caption' => $request->caption,
            'category' => $request->category,
            'created_at' => now(),
            'updated_at' => now(),

        ]);

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'gallery_upload',
            description: "Uploaded new {$request->category} ({$request->type})",
            subject: $request->team_id ? Team::find($request->team_id) : null,
            teamId: $request->team_id,
            targetUserId: null,
            properties: ['caption' => $request->caption]
        );

        return back()->with('success', 'Artifact uploaded successfully! 🚀');
    }

    // دالة حذف الصورة (لليدر بس أو صاحب الصورة)
    public function deleteGallery($id)
    {
        $item = DB::table('project_galleries')->where('id', $id)->first();
        if (!$item) return back();

        // الحماية: الليدر أو صاحب الصورة بس
        $team = Team::find($item->team_id);
        if (Auth::id() != $item->user_id && Auth::id() != $team->leader_id) {
            return back()->with('error', 'Unauthorized');
        }

        // مسح الملف من السيرفر

        DB::table('project_galleries')->where('id', $id)->delete();
        return back()->with('success', 'Item removed.');
    }

    // ==========================================
    // 19. تسليم التاسك (Submit Task with Proof)
    // ==========================================
    // 2. غيرنا (StoreTaskSubmissionRequest) وخليناها (Request) عشان نلغي الفاليديشن القديم

    public function submitTask(Request $request)
    {
        // 1. التحقق من البيانات (Logic ذكي بيغير الشروط حسب نوع التسليم)
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'submission_type' => 'required|in:file,link', // لازم يحدد نوع التسليم

            // لو النوع ملف: يبقى الملف إجباري
            'submission_file' => 'required_if:submission_type,file|nullable|file|mimes:pdf,zip,rar,doc,docx,png,jpg|max:102400',

            // لو النوع لينك: يبقى اللينك إجباري
            'link' => 'required_if:submission_type,link|nullable|url',

            'submission_comment' => 'nullable|string|max:1000'
        ]);

        try {
            $task = \App\Models\Task::findOrFail($validated['task_id']);

            // التحقق من الصلاحية
            if ($task->user_id != Auth::id()) {
                return back()->with('error', 'You are not assigned to this task.');
            }

            // 2. تجهيز البيانات للحفظ
            $updateData = [
                'submission_type' => $request->submission_type,
                'submission_comment' => $request->submission_comment,
                'status' => 'reviewing',
                'submitted_at' => now(),
                'updated_at' => now()
            ];

            // 3. معالجة الملف أو اللينك
            if ($request->submission_type === 'file') {
                // رفع الملف
                if ($request->hasFile('submission_file')) {
                    $file = $request->file('submission_file');
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '_' . Str::slug($originalName) . '.' . $extension;

                    $path = $file->storeAs('submissions', $fileName, 'public');

                    $updateData['submission_file'] = $path;
                    $updateData['submission_value'] = null; // مسح اللينك القديم لو موجود
                }
            } else {
                // حفظ اللينك
                $updateData['submission_value'] = $request->link; // تأكد إن العمود ده موجود في الداتابيز
                $updateData['submission_file'] = null; // مسح الملف القديم لو موجود
            }

            // 4. تنفيذ التحديث
            $task->update($updateData);

            return back()->with('success', 'Task submitted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Task not found.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Task submission failed: ' . $e->getMessage());
            return back()->with('error', 'Submission failed. Please try again.');
        }
    }

    // Keep approveTask and rejectTask as they were, they look correct.
    public function approveTask($id)
    {
        try {
            $task = \App\Models\Task::findOrFail($id);

            // التحقق من الصلاحية (قائد أو نائب القائد فقط)
            $member = TeamMember::where('team_id', $task->team_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$member || !in_array($member->role, ['leader', 'vice_leader'])) {
                return back()->with('error', 'Unauthorized action.');
            }

            $task->update([
                'status' => 'completed',
                'updated_at' => now()
            ]);

            return back()->with('success', 'Task approved successfully! ✅');
        } catch (\Exception $e) {
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * رفض المهمة
     */
    public function rejectTask($id)
    {
        try {
            $task = \App\Models\Task::findOrFail($id);

            // التحقق من الصلاحية (قائد أو نائب القائد فقط)
            $member = TeamMember::where('team_id', $task->team_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$member || !in_array($member->role, ['leader', 'vice_leader'])) {
                return back()->with('error', 'Unauthorized action.');
            }

            $task->update([
                'status' => 'rejected',
                'updated_at' => now()
            ]);

            return back()->with('error', 'Task rejected ❌');
        } catch (\Exception $e) {
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    /**
     * عرض الملف المرفوع
     */
    public function viewSubmissionFile($id)
    {
        $task = \App\Models\Task::findOrFail($id);

        if (!$task->submission_file) {
            abort(404, 'No submission file found');
        }

        return redirect($task->submission_file);
    }

    /**
     * تحميل الملف المرفوع
     */
    public function downloadSubmissionFile($id)
    {
        $task = \App\Models\Task::findOrFail($id);

        if (!$task->submission_file) {
            abort(404, 'No submission file found');
        }

        return redirect($task->submission_file);
    }

    /**
     * حذف تسليم المهمة (للمستخدم لتصحيح الخطأ)
     */
    public function deleteSubmission(Request $request, $id)
    {
        $task = \App\Models\Task::findOrFail($id);

        // التحقق من أن المستخدم هو صاحب المهمة
        if ($task->user_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // حذف الملف من التخزين
        if ($task->submission_file && Storage::disk('public')->exists($task->submission_file)) {
            Storage::disk('public')->delete($task->submission_file);
        }

        // إعادة تعيين حالة المهمة
        $task->update([
            'submission_file' => null,
            'submission_comment' => null,
            'status' => 'pending',
            'submitted_at' => null,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Submission deleted. You can submit again.');
    }

    // 21 دالة التسليم النهائي (الكتاب + الفيديو)
    public function submitFinalProject(Request $request)
    {
        // 1. هات التيم الخاص بالطالب اللي فاتح (أحمد أو عمر)
        $user = Auth::user();
        $team = Team::where('leader_id', $user->id)->first();

        // أمان: لو ملوش تيم أو مش هو الليدر
        if (!$team) {
            return back()->with('error', 'Only Team Leader can submit.');
        }

        // 2. التحقق من الملفات
        $request->validate([
            // تأكد إنك بتستقبل الملفات دي من الفورم
            'final_book' => 'nullable|file|mimes:pdf|max:1048576',
            'presentation' => 'nullable|file|mimes:ppt,pptx,pdf|max:1048576',
            'defense_video' => 'nullable|url',
        ]);

        // 3. رفع الملفات (لو موجودة)
        if ($request->hasFile('final_book')) {
            $bookPath = Cloudinary::upload($request->file('final_book')->getRealPath(), ['folder' => 'final_books'])->getSecurePath();
            $team->final_book_file = $bookPath; // بنسجل في جدول teams
        }

        if ($request->hasFile('presentation')) {
            $pptPath = Cloudinary::upload($request->file('presentation')->getRealPath(), ['folder' => 'presentations'])->getSecurePath();
            $team->presentation_file = $pptPath; // بنسجل في جدول teams
        }

        if ($request->defense_video) {
            $team->defense_video_link = $request->defense_video;
        }

        // 4. لو داس على زرار التسليم النهائي (القفل)
        if ($request->has('submit_finish')) {
            $team->is_fully_submitted = true;
            $team->project_phase = 'completed'; // أو finished
            $team->submitted_at = now();
        }

        $team->save(); // حفظ التغييرات في جدول التيم الخاص بالطالب ده بس

        return back()->with('success', 'Final submission updated successfully.');
    }
}
