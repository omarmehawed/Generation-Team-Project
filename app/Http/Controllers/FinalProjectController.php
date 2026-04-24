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
use App\Services\TeamNotifier;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\FundContribution;
use App\Exports\FundExport;

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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Member approved successfully.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Member request rejected.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

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
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $extension = strtolower($type[1]);
            $fileName = 'team_logos/' . uniqid() . '.' . $extension;
            \Illuminate\Support\Facades\Storage::disk('r2')->put($fileName, base64_decode($imageData));
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->url($fileName);

            $team->update(['logo' => $path]);
        }

        return back()->with('success', 'Team badge updated successfully!');
    }

    public function getTeamLogo($id)
    {
        $team = Team::findOrFail($id);

        if (!$team->logo) {
            abort(404);
        }

        if (str_starts_with($team->logo, 'http')) {
            return redirect($team->logo);
        }

        return redirect(asset('storage/' . $team->logo));
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

        // -- Debts & History --
        $currentFundId = $activeFund ? $activeFund->id : null;
        
        // 2. Unpaid History (For currently logged in user)
        $unpaidHistory = FundContribution::where('user_id', Auth::id())
            ->where('status', '!=', 'paid')
            ->whereHas('fund', function($q) use ($team, $currentFundId) {
                $q->where('team_id', $team->id);
                if ($currentFundId) {
                    $q->where('id', '!=', $currentFundId);
                }
            })
            ->with('fund')
            ->get();

        // 3. ✨ Calculate All Members' Historical Debts
        $teamMemberIds = $team->members->pluck('user_id');
        $membersDebts = FundContribution::whereIn('user_id', $teamMemberIds)
            ->where('status', '!=', 'paid')
            ->whereHas('fund', function($q) use ($team, $currentFundId) {
                $q->where('team_id', $team->id);
                if ($currentFundId) {
                    $q->where('id', '!=', $currentFundId);
                }
            })
            ->with('fund')
            ->get()
            ->groupBy('user_id')
            ->map(function($group) {
                return $group->sum(function($c) {
                    return $c->fund->amount_per_member;
                });
            });

        // Determine Role
        $myRole   = $myMemberRecord->role ?? 'member';
        
        // 4. Pending Members (For Leader's Eyes Only)
        $pendingMembers = [];
        $pendingFundPayments = [];
        if($myRole == 'leader'){
            $pendingMembers = TeamMember::where('team_id', $team->id)
                                ->where('status', 'pending')
                                ->with('user')
                                ->get();
                                
            // 🔥 Pending Fund Payments (Current + History)
            $pendingFundPayments = FundContribution::where('status', 'pending_approval')
                ->whereHas('fund', function($q) use ($team) {
                    $q->where('team_id', $team->id);
                })
                ->with(['user', 'fund'])
                ->get();
        }

        // 5. Project Components
        $components = \App\Models\ProjectComponent::where('team_id', $team->id)->latest()->get();

        // 6. Sub Leader Setup Check
        $needsSubLeaderSetup = $myMemberRecord && $myMemberRecord->is_sub_leader && is_null($myMemberRecord->team_number);
        $availableMembers = [];
        if ($needsSubLeaderSetup) {
            $myDomain = $myMemberRecord->technical_role;
            $availableMembers = $team->members()
                ->where('role', 'member')
                ->where('is_sub_leader', false) // shouldn't assign another sub leader
                ->whereNull('parent_id') // currently unassigned
                ->where('technical_role', $myDomain) // same domain
                ->get();
        }

        // 7. Workshops
        $workshops = \App\Models\Workshop::where('team_id', $team->id)->with('creator')->latest()->get();

        // 8. Task Filtering & History
        $allTasksQuery = \App\Models\Task::where('team_id', $team->id)
            ->with(['user', 'submissions.user', 'submissions.reviewer'])
            ->latest();

        if ($myRole === 'member') {
            // Members only see their own tasks
            $allTasksQuery->where('user_id', Auth::id());
        } elseif ($myRole === 'vice_leader') {
            $myDomain = strtolower($myMemberRecord->technical_role);
            if (in_array($myDomain, ['software', 'hardware'])) {
                $allTasksQuery->where(function($q) use ($myDomain, $team) {
                    $q->where('technical_role', $myDomain)
                      ->orWhere(function($sq) use ($myDomain, $team) {
                          $sq->whereNull('technical_role')
                             ->whereHas('user.teamMemberships', function($mq) use ($myDomain, $team) {
                                 $mq->where('team_id', $team->id)
                                    ->where('technical_role', $myDomain);
                             });
                      });
                });
            }
        }

        $allTasks = $allTasksQuery->get();
        $team->setRelation('tasks', $allTasks);

        // Group tasks by Title for History Log
        $tasksHistory = $allTasks->where('status', 'completed')->groupBy('title');

        return view('final_project.dashboard', compact(
            'team',
            'project',
            'activeFund',
            'fundsHistory',
            'membersDebts',
            'unpaidHistory',
            'myRole',
            'myMemberRecord',
            'pendingMembers',
            'pendingFundPayments',
            'components',
            'needsSubLeaderSetup',
            'availableMembers',
            'workshops',
            'tasksHistory'
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
        $existingMember = $team->members()->where('user_id', $userToAdd->id)->exists();
        if ($existingMember) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'This user is already in your team.'], 422);
            return back()->with('error', 'This user is already in your team.');
        }

        // 4. نتأكد إنه مش في أي تيم تاني لنفس المشروع (عشان ميكونش بوشين)
        $alreadyMemberSomewhereElse = TeamMember::where('user_id', $userToAdd->id)
            ->whereHas('team', function ($q) use ($team) {
                $q->where('project_id', $team->project_id);
            })->exists();

        if ($alreadyMemberSomewhereElse) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'This user is already a member of another team.'], 422);
            return back()->with('error', 'This user is already a member of another team.');
        }

        // 5. نتأكد إن التيم مش كامل (100 عضو مثلاً)
        if ($team->members()->count() >= 20) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Team is full.'], 422);
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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Member added successfully!',
                'redirect' => route('final_project.dashboard', $team->id) . '#team-section'
            ]);
        }

        return back()->with('success', 'Member added successfully!');
    }

    // ==========================================
    // Sub Leader Setup
    // ==========================================
    public function subLeaderSetup(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'team_number' => 'required|integer|min:1',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:team_members,id'
        ]);

        $currentUser = Auth::user();
        $team = Team::findOrFail($request->team_id);
        
        $myMemberRecord = $team->members()->where('user_id', $currentUser->id)->first();
        if (!$myMemberRecord || !$myMemberRecord->is_sub_leader) {
            abort(403, 'Unauthorized. Only Sub Leaders can setup teams.');
        }

        // Check if team number is unique in this main team
        $existingSubLeader = TeamMember::where('team_id', $team->id)
            ->where('is_sub_leader', true)
            ->where('team_number', $request->team_number)
            ->exists();

        if ($existingSubLeader) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Team Number ' . $request->team_number . ' is already taken by another Sub Leader.'], 422);
            return back()->with('error', 'Team Number ' . $request->team_number . ' is already taken by another Sub Leader.');
        }

        \DB::transaction(function () use ($request, $myMemberRecord) {
            // Update Sub Leader's team number
            $myMemberRecord->update(['team_number' => $request->team_number]);

            // Assign parent_id and team_number to selected members
            TeamMember::whereIn('id', $request->member_ids)->update([
                'parent_id' => $myMemberRecord->id,
                'team_number' => $request->team_number
            ]);
        });

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your team has been set up successfully!',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your team has been set up successfully!',
                'redirect' => route('final_project.dashboard', $team->id) . '#team-section'
            ]);
        }

        return back()->with('success', 'Your team has been set up successfully!');
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
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Only the Team Leader can remove members.'], 403);
            return back()->with('error', 'Only the Team Leader can remove members.');
        }

        // 2. ممنوع الليدر يمسح نفسه من الزرار ده (يستخدم Leave Team)
        if ($request->user_id == $team->leader_id) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'You cannot remove yourself. Use "Leave Team" instead.'], 422);
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

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Member removed successfully.',
                    'redirect' => route('final_project.dashboard', $team->id) . '#team-section'
                ]);
            }

            return back()->with('success', 'Member removed successfully.');
        }

        if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Member not found in this team.'], 404);
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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Report submitted successfully.');
    }

    // 9. Create Team View (Unused if using modal, but kept for reference)
    public function createTeamView($projectId)
    {
        return redirect()->route('final_project.start');
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
            $storedPath = $request->file('proposal_file')->store('proposals', 'r2');
            $filePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proposal submitted successfully! Waiting for approval.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

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

    // 10. تعديل أدوار وتخصصات العضو (Updated with Strict Permissions)
    public function updateMemberStatus(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,vice_leader', // الدور الإداري
            'is_sub_leader' => 'nullable|boolean',
            'technical_role' => 'required|in:general,software,hardware', // التخصص التقني
            'extra_role' => 'nullable|in:none,presentation,reports,marketing,project_book', // المسؤوليات الإضافية
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:view_team_funds,wallet_management,deposit_requests',
            'can_manage_components' => 'nullable|boolean',
            'can_manage_expenses'   => 'nullable|boolean',
            'can_access_join_requests' => 'nullable|boolean',
            'can_manage_quizzes' => 'nullable|boolean',
        ]);

        $currentUser = Auth::user();
        $team = Team::findOrFail($request->team_id);

        // 1. Get Member Records
        $myMemberRecord = $team->members()->where('user_id', $currentUser->id)->first();
        $targetMemberRecord = $team->members()->where('user_id', $request->user_id)->first();

        if (!$targetMemberRecord) {
            return back()->with('error', 'Member not found in this team.');
        }

        // 2. Authorization Check
        $isLeader = $team->leader_id == $currentUser->id;
        $isViceLeader = ($myMemberRecord->role ?? '') == 'vice_leader';

        if (!$isLeader && !$isViceLeader) {
            abort(403, 'Unauthorized action.');
        }

        // 3. User cannot change their own role/permissions
        if ($request->user_id == $currentUser->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        // 4. VICE LEADER RESTRICTIONS
        if ($isViceLeader) {
            // A. Vice Leader can only manage people who are currently 'member'
            if ($targetMemberRecord->role !== 'member') {
                return back()->with('error', 'You can only manage regular Members. You cannot manage Leaders or other Vice Leaders.');
            }

            // B. Vice Leader can only set role to 'member' (They cannot promote anyone to Admin/VL)
            if ($request->role !== 'member') {
                return back()->with('error', 'You do not have permission to promote users to administrative roles.');
            }

            // C. Vice Leader cannot edit permissions or extra roles
            // We ignore whatever they sent and keep the existing ones
            $existingPermissions = is_string($targetMemberRecord->user->permissions) ? json_decode($targetMemberRecord->user->permissions, true) : ($targetMemberRecord->user->permissions ?? []);
            $request->merge([
                'permissions' => $existingPermissions,
                'extra_role' => $targetMemberRecord->extra_role ?? 'none',
                'can_manage_components' => $targetMemberRecord->can_manage_components,
                'can_manage_expenses'   => $targetMemberRecord->can_manage_expenses,
                'can_access_join_requests' => $targetMemberRecord->can_access_join_requests,
                'can_manage_quizzes' => $targetMemberRecord->can_manage_quizzes,
            ]);

            // D. Domain Scoping (Hardware/Software)
            // Note: Vice Leader CA N promote members to Sub Leader now within their domain.
            $myDomain = strtolower($myMemberRecord->technical_role ?? 'general');
            $targetDomain = strtolower($targetMemberRecord->technical_role ?? 'general');
            
            // If Vice Leader is Hardware/Software, they can ONLY manage members in THAT domain
            if (in_array($myDomain, ['hardware', 'software'])) {
                if ($targetDomain !== $myDomain && $targetDomain !== 'general') {
                    return back()->with('error', "You can only manage members in the " . ucfirst($myDomain) . " team.");
                }
                // Check if they are trying to move them out of their domain
                if (strtolower($request->technical_role) !== $myDomain) {
                    return back()->with('error', "You cannot move members to other technical teams.");
                }
            } else {
                // General Vice Leader logic if needed (Currently we restricted them in UI, but if any exist:)
                // They can manage anyone if no specific domain is set for them? 
                // As per user: "No specific domain if General" -> So they can manage but let's be safe.
            }
        }

        // 5. Build Update Data
        $updateData = [
            'role' => $request->role,
            'is_sub_leader' => $request->has('is_sub_leader') ? (bool) $request->is_sub_leader : false,
            'technical_role' => $request->technical_role,
            'extra_role' => ($request->extra_role == 'none' || !$request->extra_role) ? null : $request->extra_role,
            'can_manage_components' => $request->has('can_manage_components') ? (bool) $request->can_manage_components : false,
            'can_manage_expenses'   => $request->has('can_manage_expenses') ? (bool) $request->can_manage_expenses : false,
            'can_access_join_requests' => $request->has('can_access_join_requests') ? (bool) $request->can_access_join_requests : false,
            'can_manage_quizzes' => $request->has('can_manage_quizzes') ? (bool) $request->can_manage_quizzes : false,
        ];

        // Perform Update in team_members
        $targetMemberRecord->update($updateData);

        // Update Permissions in users table
        $targetUser = $targetMemberRecord->user;
        $permissions = $request->permissions ?? [];
        $targetUser->permissions = $permissions;
        $targetUser->save();

        return back()->with('success', 'Member roles and permissions updated successfully ✨');
    }



    // ==========================================
    // 11. إضافة مصروفات
    // ==========================================
    public function storeExpense(Request $request)
    {
        $request->validate([
            'team_id'           => 'required|exists:teams,id',
            'component_id'      => 'nullable|array',
            'custom_item_name'  => 'nullable|array',
            'shop_name'         => 'required|string|max:255',
            'price_per_unit'    => 'required|array',
            'price_per_unit.*'  => 'numeric|min:0.01',
            'quantity'          => 'required|array',
            'quantity.*'        => 'integer|min:1',
            'receipt'           => 'required|image|max:5120',
        ]);

        $team = Team::findOrFail($request->team_id);

        // Authorization: Leader or delegated permission
        $myRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myRecord || ($myRecord->role !== 'leader' && !$myRecord->can_manage_expenses)) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return back()->with('error', 'You do not have permission to add expenses.');
        }

        // Upload receipt image ONCE
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $storedPath = $request->file('receipt')->store('receipts', 'r2');
            $receiptPath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        $componentIds    = $request->component_id ?? [];
        $customItemNames = $request->custom_item_name ?? [];
        $prices         = $request->price_per_unit;
        $quantities     = $request->quantity;

        DB::beginTransaction();
        try {
            $totalBatchAmount = 0;
            $itemsList = [];

            // We iterate based on the number of prices/quantities provided
            foreach ($prices as $index => $price) {
                $pricePerUnit = (float) $price;
                $qty          = (int) $quantities[$index];
                $amount       = $pricePerUnit * $qty;
                $totalBatchAmount += $amount;

                $compId = $componentIds[$index] ?? null;
                $itemName = $customItemNames[$index] ?? null;

                if ($compId && $compId != 'custom') {
                    $comp = \App\Models\ProjectComponent::where('id', $compId)
                        ->where('team_id', $team->id)
                        ->first();
                    if ($comp) {
                        $itemName = $comp->name;
                    }
                }

                if (!$itemName) continue; // Safety check

                $itemsList[] = "{$itemName} x{$qty}";

                DB::table('project_expenses')->insert([
                    'team_id'        => $team->id,
                    'user_id'        => Auth::id(),
                    'component_id'   => ($compId && $compId != 'custom') ? $compId : null,
                    'item'           => $itemName,
                    'shop_name'      => $request->shop_name,
                    'price_per_unit' => $pricePerUnit,
                    'quantity'       => $qty,
                    'amount'         => $amount,
                    'receipt_path'   => $receiptPath,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // 🔍 LOG ACTIVITY
            ActivityLogger::log(
                action: 'expense_batch_added',
                description: "Added batch expenses from {$request->shop_name}: " . implode(', ', $itemsList),
                teamId: $team->id,
                properties: ['total' => $totalBatchAmount, 'count' => count($itemsList)]
            );

            // 🔔 Notify ALL team members
            TeamNotifier::notifyAll($team, [
                'title'      => '💸 New Batch Expenses Added',
                'message'    => Auth::user()->name . ' recorded ' . count($componentIds) . ' expenses totaling ' . number_format($totalBatchAmount, 2) . ' EGP from ' . $request->shop_name,
                'icon'       => 'fas fa-receipt',
                'color'      => 'text-red-500',
                'action_url' => route('final_project.dashboard', $team->id) . '#budget-section',
                'type'       => 'info'
            ], [Auth::id()]);

            DB::commit();
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Batch expenses recorded successfully! 💸',
                    'redirect' => back()->getTargetUrl()
                ]);
            }
            return back()->with('success', 'Batch expenses recorded successfully! 💸');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save batch expenses: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to save batch expenses: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 11b. إضافة عنصر مكون (Add Project Component)
    // ==========================================
    public function storeComponent(Request $request)
    {
        $request->validate([
            'team_id'     => 'required|exists:teams,id',
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:5120',
        ]);

        $team = Team::findOrFail($request->team_id);

        // Authorization: Leader or delegated permission
        $myRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myRecord || ($myRecord->role !== 'leader' && !$myRecord->can_manage_components)) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'You do not have permission to manage components.'], 403);
            return back()->with('error', 'You do not have permission to manage components.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('project_components', 'r2');
            $imagePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        \App\Models\ProjectComponent::create([
            'team_id'     => $team->id,
            'name'        => $request->name,
            'description' => $request->description,
            'image_path'  => $imagePath,
        ]);

        ActivityLogger::log(
            action: 'component_added',
            description: "Added project component: {$request->name}",
            subject: $team,
            teamId: $team->id,
            targetUserId: null
        );

        // 🔔 Notify ALL team members
        TeamNotifier::notifyAll($team, [
            'title'      => '🔩 New Component Added',
            'message'    => Auth::user()->name . ' added a new component: "' . $request->name . '" — ' . \Illuminate\Support\Str::limit($request->description, 80),
            'icon'       => 'fas fa-microchip',
            'color'      => 'text-blue-500',
            'action_url' => route('final_project.dashboard', $team->id) . '#budget-section',
            'type'       => 'info'
        ], [Auth::id()]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Component added successfully!',
                'redirect' => route('final_project.dashboard', $team->id) . '#budget-section'
            ]);
        }

        return back()->with('success', 'Component added successfully! 🔩');
    }

    public function updateComponent(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:5120',
        ]);

        $component = \App\Models\ProjectComponent::findOrFail($id);
        $team = $component->team;

        // Authorization: Leader or delegated permission
        $myRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myRecord || ($myRecord->role !== 'leader' && !$myRecord->can_manage_components)) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'You do not have permission to manage components.'], 403);
            return back()->with('error', 'You do not have permission to manage components.');
        }

        $imagePath = $component->image_path;
        if ($request->hasFile('image')) {
            // Optional: Delete old image if it exists
            if ($imagePath && str_contains($imagePath, 'storage.googleapis.com') === false && str_contains($imagePath, 'http') === true) {
                // Logic to delete from R2 if needed, but usually we just overwrite or leave it. 
                // For now, let's just upload the new one.
            }
            $storedPath = $request->file('image')->store('project_components', 'r2');
            $imagePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        $component->update([
            'name'        => $request->name,
            'description' => $request->description,
            'image_path'  => $imagePath,
        ]);

        ActivityLogger::log(
            action: 'component_updated',
            description: "Updated project component: {$request->name}",
            subject: $team,
            teamId: $team->id,
            targetUserId: null
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Component updated successfully!',
                'redirect' => route('final_project.dashboard', $team->id) . '#budget-section'
            ]);
        }

        return back()->with('success', 'Component updated successfully! 🛠️');
    }

    public function destroyComponent($id)
    {
        $component = \App\Models\ProjectComponent::findOrFail($id);
        $team = $component->team;

        // Authorization: Leader or delegated permission
        $myRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myRecord || ($myRecord->role !== 'leader' && !$myRecord->can_manage_components)) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return back()->with('error', 'You do not have permission to manage components.');
        }

        $name = $component->name;
        $component->delete();

        ActivityLogger::log(
            action: 'component_deleted',
            description: "Deleted project component: {$name}",
            subject: $team,
            teamId: $team->id,
            targetUserId: null
        );

        if (request()->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Component deleted successfully! 🗑️',
                'redirect' => route('final_project.dashboard', $team->id) . '#budget-section'
            ]);
        }

        return back()->with('success', 'Component deleted successfully! 🗑️');
    }
    public function updateExpense(Request $request, $id)
    {
        $request->validate([
            'item'           => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'shop_name'      => 'required|string|max:255',
            'receipt'        => 'nullable|image|max:5120',
            'quantity'       => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric|min:0.01',
        ]);

        $expense = \App\Models\ProjectExpense::findOrFail($id);
        $team = $expense->team;

        // Authorization: Leader or delegated permission
        $myRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myRecord || ($myRecord->role !== 'leader' && !$myRecord->can_manage_expenses)) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return back()->with('error', 'You do not have permission to manage expenses.');
        }

        $quantity = (int) $request->quantity;
        $pricePerUnit = (float) $request->price_per_unit;
        $totalAmount = $quantity * $pricePerUnit;

        $receiptPath = $expense->receipt_path;
        if ($request->hasFile('receipt')) {
            $storedPath = $request->file('receipt')->store('receipts', 'r2');
            $receiptPath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        $expense->update([
            'item'           => $request->item,
            'amount'         => $totalAmount,
            'shop_name'      => $request->shop_name,
            'receipt_path'   => $receiptPath,
            'quantity'       => $quantity,
            'price_per_unit' => $pricePerUnit,
        ]);

        ActivityLogger::log(
            action: 'expense_updated',
            description: "Updated expense: {$expense->item} from {$request->shop_name}",
            subject: $team,
            teamId: $team->id,
            targetUserId: null
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Expense updated successfully! 💸',
                'redirect' => route('final_project.dashboard', $team->id) . '#budget-section'
            ]);
        }

        return back()->with('success', 'Expense updated successfully! 💸');
    }

    public function destroyExpense($id)
    {
        $expense = \App\Models\ProjectExpense::findOrFail($id);
        $team = $expense->team;

        // Authorization: Leader or delegated permission
        $myRecord = $team->members->where('user_id', Auth::id())->first();
        if (!$myRecord || ($myRecord->role !== 'leader' && !$myRecord->can_manage_expenses)) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return back()->with('error', 'You do not have permission to delete expenses.');
        }

        $itemName = $expense->item;
        $amount = $expense->amount;

        $expense->delete();

        ActivityLogger::log(
            action: 'expense_deleted',
            description: "Deleted expense: {$itemName}",
            subject: $team,
            teamId: $team->id,
            targetUserId: null
        );

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully! 🗑️',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Expense deleted successfully! 🗑️');
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

        // 🔔 Notify ALL team members about the fund request
        $deadline = \Carbon\Carbon::parse($request->deadline)->format('M d, Y');
        TeamNotifier::notifyAll($team, [
            'title'      => '💰 New Fund Collection Started',
            'message'    => 'Leader ' . Auth::user()->name . ' started a fund collection: "' . $request->title . '" — ' . number_format($request->amount_per_member, 2) . ' EGP per member. Deadline: ' . $deadline,
            'icon'       => 'fas fa-hand-holding-usd',
            'color'      => 'text-green-600',
            'action_url' => route('final_project.dashboard', $team->id) . '#budget-section',
            'type'       => 'warning'
        ], [Auth::id()]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fund request created and members notified! 💰',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Fund request created and members notified! 💰');
    }


    // ==========================================
    // 13. [NEW] Submit Payment (Member Side)
    // ==========================================
    public function submitPayment(Request $request)
    {
        $request->validate([
            'contribution_id' => 'required|exists:fund_contributions,id',
            'payment_method' => 'required|in:cash,vodafone_cash,instapay,wallet',
        ]);

        $contrib = \App\Models\FundContribution::findOrFail($request->contribution_id);

        // Authorization: Ensure the user owns this contribution
        if ($contrib->user_id != Auth::id()) {
             if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
             return back()->with('error', 'Unauthorized action.');
        }

        // Specific Validations based on Method
        if (in_array($request->payment_method, ['vodafone_cash', 'instapay'])) {
            $request->validate([
                'amount_transferred' => 'required|numeric',
                'from_number' => 'required|string',
                'transaction_date' => 'required|date',
                'transaction_time' => 'required',
                'proof_image' => 'required|image|max:5120'
            ]);
        } elseif ($request->payment_method == 'cash') {
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

        if (in_array($request->payment_method, ['vodafone_cash', 'instapay'])) {
            // Upload Proof
            if ($request->hasFile('proof_image')) {
                $storedPath = $request->file('proof_image')->store('payment_proofs', 'r2');
                $updateData['payment_proof'] = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
            }
            $updateData['amount'] = $request->amount_transferred; // Assuming column is 'amount' (it is in schema)
            $updateData['from_number'] = $request->from_number;
            $updateData['transaction_date'] = $request->transaction_date;
            $updateData['transaction_time'] = $request->transaction_time;
        } elseif ($request->payment_method == 'wallet') {
             // Wallet logic: No special fields needed for submission, just mark as pending
             $updateData['notes'] = "Payment via Wallet Balance";
        } else {
             $updateData['notes'] = $request->notes;
        }

        $contrib->update($updateData);

        $team = Team::find($contrib->fund->team_id);

        // 🔍 LOG ACTIVITY
        ActivityLogger::log(
            action: 'fund_payment_submitted',
            description: "Submitted payment for review: '{$contrib->fund->title}' via {$request->payment_method}",
            subject: $contrib,
            teamId: $team->id,
            targetUserId: null,
            properties: [
                'amount' => $request->amount_transferred ?? $contrib->fund->amount_per_member,
                'method' => $request->payment_method,
                'fund_title' => $contrib->fund->title
            ]
        );

        // Notify Leader
        $leader = User::find($team->leader_id);
        
        // Notify Leader
        if ($leader) {
             $leader->notify(new BatuNotification([
                'title'      => '💸 Payment Submitted for Review',
                'message'    => Auth::user()->name . ' submitted a payment for "' . $contrib->fund->title . '" (' . $request->payment_method . '). Please review.',
                'icon'       => 'fas fa-file-invoice-dollar',
                'color'      => 'text-yellow-500',
                'action_url' => route('final_project.dashboard', $team->id) . '#budget-section',
                'type'       => 'info'
            ]));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment submitted! Waiting for leader approval.',
                'redirect' => route('final_project.dashboard', $team->id) . '#budget-section'
            ]);
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
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized. Only Team Leader can review.'], 403);
            return back()->with('error', 'Unauthorized. Only Team Leader can review.');
        }

        $user = User::find($contrib->user_id);

        if ($request->action == 'approve') {
            // Deduct from wallet if method is wallet
            if ($contrib->payment_method === 'wallet') {
                if ($user->wallet_balance < $contrib->fund->amount_per_member) {
                    if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Insufficient wallet balance for this member.'], 422);
                    return back()->with('error', 'Insufficient wallet balance for this member.');
                }
                
                $user->decrement('wallet_balance', $contrib->fund->amount_per_member);
                
                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'admin_id' => Auth::id(),
                    'type' => 'withdrawal',
                    'amount' => $contrib->fund->amount_per_member,
                    'balance_after' => $user->wallet_balance,
                    'notes' => "Fund Payment: " . $contrib->fund->title,
                ]);
            }

            $contrib->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // 🔍 LOG ACTIVITY
            ActivityLogger::log(
                action: 'fund_payment_approved',
                description: "Approved payment of " . number_format($contrib->amount ?? $contrib->fund->amount_per_member) . " EGP from {$user->name} for '{$contrib->fund->title}'",
                subject: $contrib,
                teamId: $team->id,
                targetUserId: $user->id,
                properties: [
                    'amount' => $contrib->amount ?? $contrib->fund->amount_per_member,
                    'method' => $contrib->payment_method,
                    'fund_title' => $contrib->fund->title
                ]
            );

            // Notify Member (Success)
            $user->notify(new BatuNotification([
                'title'      => '✅ Payment Approved',
                'message'    => 'Your payment for "' . $contrib->fund->title . '" has been confirmed by the Leader. Thank you!',
                'icon'       => 'fas fa-check-circle',
                'color'      => 'text-green-500',
                'action_url' => route('final_project.dashboard', $team->id) . '#budget-section',
                'type'       => 'success'
            ]));

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment approved successfully.',
                    'redirect' => back()->getTargetUrl()
                ]);
            }
            return back()->with('success', 'Payment approved successfully.');
        } else {
            $contrib->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);

            // Notify Member (Rejected)
            $user->notify(new BatuNotification([
                'title'      => '❌ Payment Rejected',
                'message'    => 'Your payment for "' . $contrib->fund->title . '" was rejected. Reason: ' . $request->rejection_reason,
                'icon'       => 'fas fa-times-circle',
                'color'      => 'text-red-500',
                'action_url' => route('final_project.dashboard', $team->id) . '#budget-section',
                'type'       => 'alert'
            ]));

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment rejected.',
                    'redirect' => back()->getTargetUrl()
                ]);
            }

            return back()->with('success', 'Payment rejected.');
        }
    }
    public function forceWalletPayment(Request $request)
    {
        $request->validate([
            'contribution_id' => 'required|exists:fund_contributions,id'
        ]);

        $contrib = FundContribution::with('fund.team', 'user')->findOrFail($request->contribution_id);
        $team = $contrib->fund->team;
        $user = $contrib->user;

        // Security check
        if (Auth::id() != $team->leader_id) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized: Only Team Leader can force payment.'], 403);
            return back()->with('error', 'Unauthorized: Only Team Leader can force payment.');
        }

        if ($contrib->status === 'paid') {
            return back()->with('info', 'This contribution is already paid.');
        }

        $amount = $contrib->fund->amount_per_member;

        if ($user->wallet_balance < $amount) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Member has insufficient wallet balance (' . number_format($user->wallet_balance, 2) . ' EGP).'], 422);
            return back()->with('error', 'Member has insufficient wallet balance (' . number_format($user->wallet_balance, 2) . ' EGP).');
        }

        DB::transaction(function() use ($user, $contrib, $amount, $team) {
            // Deduct
            $user->decrement('wallet_balance', $amount);

            // Log Transaction
            \App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_after' => $user->wallet_balance,
                'notes' => "Forced Fund Payment: " . $contrib->fund->title,
            ]);

            // Mark Paid
            $contrib->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'wallet',
                'notes' => 'Forced by Leader',
            ]);

            // 🔍 LOG ACTIVITY
            ActivityLogger::log(
                action: 'fund_payment_forced',
                description: "Deducted " . number_format($amount) . " EGP from {$user->name}'s wallet for '{$contrib->fund->title}' (History Payment)",
                subject: $contrib,
                teamId: $team->id,
                targetUserId: $user->id,
                properties: [
                    'amount' => $amount,
                    'method' => 'wallet',
                    'fund_title' => $contrib->fund->title
                ]
            );
        });

        // Notify Member
        $user->notify(new BatuNotification([
            'title'      => '💰 Automated Wallet Deduction',
            'message'    => 'Your leader has deducted ' . number_format($amount, 2) . ' EGP from your wallet for "' . $contrib->fund->title . '".',
            'icon'       => 'fas fa-wallet',
        ]));

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully deducted ' . number_format($amount, 2) . ' EGP from ' . $user->name . '\'s wallet.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Successfully deducted ' . number_format($amount, 2) . ' EGP from ' . $user->name . '\'s wallet.');
    }


    public function updatePaymentSettings(Request $request, Team $team)
    {
        if (Auth::id() != $team->leader_id) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            abort(403, 'Unauthorized');
        }


        $request->validate([
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'in:vodafone_cash,instapay,cash,wallet'
        ]);

        $team->update([
            'payment_methods' => $request->payment_methods ?? []
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment settings updated successfully! ⚙️',
                'redirect' => route('final_project.dashboard', $team->id) . '#budget-section'
            ]);
        }

        return back()->with('success', 'Payment settings updated successfully! ⚙️');
    }

    // ==========================================
    // 13. تسجيل دفع عضو (Mark as Paid) -> DEPRECATED/UPDATED
    // ==========================================
    public function markPaid(Request $request)
    {
        $request->validate([
            'contribution_id' => 'required|exists:fund_contributions,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $contrib = FundContribution::with(['fund', 'user'])->findOrFail($request->contribution_id);
        $team = Team::findOrFail($contrib->fund->team_id);

        // Security check: Only Leader or Admin
        if ($user->id !== $team->leader_id && $user->role !== 'admin') {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Only the Team Leader can mark payments manually.'], 403);
            return back()->with('error', 'Only the Team Leader can mark payments manually.');
        }

        // Action: Instant Settlement
        $contrib->update([
            'status' => 'paid',
            'payment_method' => 'cash', 
            'paid_at' => now(),
            'notes' => $request->notes ?? 'Manual cash settlement by Leader'
        ]);

        // Audit Log
        if (class_exists(\App\Services\ActivityLogger::class)) {
            \App\Services\ActivityLogger::log(
                'fund_payment_marked_paid',
                "Leader manually settled contribution #{$contrib->id} for {$contrib->user->name}",
                $team
            );
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Payment for {$contrib->user->name} settled successfully! 💵✅",
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', "Payment for {$contrib->user->name} settled successfully! 💵✅");
    }

    public function exportFunds(Request $request)
    {
        $type = $request->query('type', 'paid');
        $teamId = $request->query('team_id');
        $user = Auth::user();
        
        // Permission check: Leader or Admin Role
        $isAdmin = $user->role === 'admin';
        
        if ($teamId && $isAdmin) {
            $team = Team::with('members.user')->find($teamId);
        } else {
            $teamMember = TeamMember::where('user_id', $user->id)->first();
            if (!$teamMember || $teamMember->role !== 'leader') {
                return back()->with('error', 'Only the Team Leader can export data.');
            }
            $team = Team::with('members.user')->find($teamMember->team_id);
        }

        if (!$team) {
            return back()->with('error', 'Team not found.');
        }

        $activeFund = ProjectFund::where('team_id', $team->id)->latest()->first();
        
        if (!$activeFund) {
            return back()->with('error', 'No active fund request found for this team.');
        }

        $data = [];
        
        if ($type === 'paid') {
            $contributions = FundContribution::with('user')
                ->where('fund_id', $activeFund->id)
                ->where('status', 'paid')
                ->get();
            
            foreach ($contributions as $c) {
                if (!$c->user) continue;
                $data[] = [
                    'Member Name' => $c->user->name,
                    'Academic Number' => Str::before($c->user->email, '@'),
                    'Payment Status' => 'Paid',
                    'Amount Paid' => $activeFund->amount_per_member . ' EGP',
                    'Payment Date' => $c->updated_at->format('Y-m-d H:i')
                ];
            }
        } else {
            // Unpaid members including historical debt
            foreach ($team->members as $member) {
                $u = $member->user;
                if (!$u) continue;

                // Check if paid current active fund
                $hasPaidActive = FundContribution::where('fund_id', $activeFund->id)
                    ->where('user_id', $u->id)
                    ->where('status', 'paid')
                    ->exists();
                
                // Calculate historical debt
                $allFunds = ProjectFund::where('team_id', $team->id)->get();
                $totalDebt = 0;
                $unpaidFundsNames = [];

                foreach ($allFunds as $fund) {
                    $isPaid = FundContribution::where('fund_id', $fund->id)
                        ->where('user_id', $u->id)
                        ->where('status', 'paid')
                        ->exists();
                    
                    if (!$isPaid) {
                        $totalDebt += $fund->amount_per_member;
                        $unpaidFundsNames[] = $fund->title;
                    }
                }

                if ($totalDebt > 0) {
                    $data[] = [
                        'Member Name' => $u->name,
                        'Academic Number' => Str::before($u->email, '@'),
                        'Status' => $hasPaidActive ? 'History Debt Only' : 'Unpaid + History Debt',
                        'Total Debt (EGP)' => $totalDebt,
                        'Pending Funds' => implode(', ', $unpaidFundsNames)
                    ];
                }
            }
        }

        if (empty($data)) {
            return back()->with('error', 'No members found for this criteria.');
        }

        return Excel::download(new FundExport($data), 'Fund_'.ucfirst($type).'_'.now()->format('Y-m-d').'.xlsx');
    }




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
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Only the Leader or Report Manager can submit weekly reports.'], 403);
            return back()->with('error', 'Only the Leader or Report Manager can submit weekly reports.');
        }

        // Handle File Upload
        $filePath = null;
        if ($request->hasFile('report_file')) {
            $storedPath = $request->file('report_file')->store('weekly_reports', 'r2');
            $filePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
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

        // Notify the TA (Teaching Assistant)
        $ta = User::find(Team::find($request->team_id)->ta_id);
        if ($ta) {
            $ta->notify(new BatuNotification([
                'title'      => '📊 Weekly Report Submitted',
                'message'    => $user->name . ' submitted Week ' . $request->week_number . ' report for team ' . (Team::find($request->team_id)->project->name ?? 'Team') . '.',
                'icon'       => 'fas fa-calendar-check',
                'color'      => 'text-blue-500',
                'action_url' => route('staff.team.manage', $request->team_id),
                'type'       => 'info'
            ]));
        }

        // 🔔 Notify ALL team members about the new report
        $teamObj = Team::find($request->team_id);
        TeamNotifier::notifyAll($teamObj, [
            'title'      => '📋 New Weekly Report Submitted',
            'message'    => $user->name . ' submitted the Week ' . $request->week_number . ' report. Check the progress update.',
            'icon'       => 'fas fa-file-alt',
            'color'      => 'text-indigo-500',
            'action_url' => route('final_project.dashboard', $request->team_id) . '#reports-section',
            'type'       => 'info'
        ], [$user->id]); // don't notify the submitter

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Weekly Report submitted successfully 📝',
                'redirect' => route('final_project.dashboard', $request->team_id) . '#reports-section'
            ]);
        }

        return back()->with('success', 'Weekly Report submitted successfully 📝');
    }

    public function updateWeeklyReport(Request $request, $id)
    {
        $request->validate([
            'week_number' => 'required|integer',
            'report_date' => 'required|date',
            'achievements' => 'required|string',
            'plans' => 'required|string',
            'challenges' => 'nullable|string',
            'report_file' => 'nullable|file|max:20480'
        ]);

        $report = DB::table('weekly_reports')->where('id', $id)->first();
        if (!$report) abort(404);

        if ($report->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $filePath = $report->file_path;
        if ($request->hasFile('report_file')) {
            $storedPath = $request->file('report_file')->store('weekly_reports', 'r2');
            $filePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        DB::table('weekly_reports')->where('id', $id)->update([
            'week_number' => $request->week_number,
            'report_date' => $request->report_date,
            'achievements' => $request->achievements,
            'plans' => $request->plans,
            'challenges' => $request->challenges,
            'file_path' => $filePath,
            'updated_at' => now(),
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Report updated successfully!']);
        }

        return back()->with('success', 'Report updated successfully!');
    }

    public function deleteWeeklyReport($id)
    {
        $report = DB::table('weekly_reports')->where('id', $id)->first();
        if (!$report) abort(404);

        if (Auth::id() != $report->user_id) {
            abort(403, 'Unauthorized');
        }

        DB::table('weekly_reports')->where('id', $id)->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Report deleted successfully!']);
        }

        return back()->with('success', 'Report deleted successfully!');
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
        if (Auth::id() != $team->leader_id) {
            if (request()->ajax()) return response()->json(['success' => false, 'message' => 'Only Leader can book.'], 403);
            return back()->with('error', 'Only Leader can book.');
        }

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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Request sent to supervisor 📅',
                'redirect' => route('final_project.dashboard', $team->id) . '#supervision-section'
            ]);
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

        $team = Team::find($request->team_id);
        $currentUserRole = \App\Models\TeamMember::where('team_id', $team->id)->where('user_id', Auth::id())->first();

        $domain = null;
        $team_number = null;

        if ($currentUserRole && $currentUserRole->role == 'vice_leader') {
            $domain = $currentUserRole->technical_role;
        } elseif ($currentUserRole && $currentUserRole->role == 'sub_leader') {
            $team_number = $currentUserRole->team_number;
        }

        $meeting = \App\Models\Meeting::create([
            'team_id' => $request->team_id,
            'topic' => $request->topic,
            'meeting_date' => $request->meeting_date,
            'mode' => $request->mode,
            'meeting_link' => $request->mode == 'online' ? $request->meeting_link : null,
            'location' => $finalLocation,
            'type' => 'internal',
            'status' => 'confirmed',
            'created_by' => Auth::id(),
            'domain' => $domain,
            'team_number' => $team_number
        ]);

        // Fetch valid attendees based on hierarchy scope
        $membersQuery = $team->members();
        if ($domain) {
            $membersQuery->where('technical_role', $domain)->where('role', '!=', 'leader'); // Exclude leader from domain-specific defaults usually, or keep them if they are general? We can just filter by domain.
        } elseif ($team_number) {
            $membersQuery->where('team_number', $team_number);
        }

        $targetMembers = $membersQuery->get();

        // سجل الحضور
        foreach ($targetMembers as $member) {
            \App\Models\MeetingAttendance::create([
                'meeting_id' => $meeting->id,
                'user_id' => $member->user_id,
                'is_present' => false
            ]);
        }

        // إرسال الإشعارات
        foreach ($targetMembers as $member) {
            if ($member->user_id == Auth::id()) continue; // Don't notify the creator

            $userToNotify = \App\Models\User::find($member->user_id);
            if ($userToNotify) {
                $userToNotify->notify(new \App\Notifications\BatuNotification([
                    'title'   => 'New Team Meeting 📅',
                    'body'    => 'Topic: ' . $request->topic . ' on ' . $request->meeting_date,
                    'icon'    => 'fas fa-users',
                    'color'   => 'text-indigo-500',
                    'url'     => route('final_project.dashboard', $team->id),
                    'type'    => 'info'
                ]));
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Internal meeting scheduled successfully.',
                'redirect' => route('final_project.dashboard', $team->id) . '#meetings-section'
            ]);
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
        
        $currentUserRole = \App\Models\TeamMember::where('team_id', $meeting->team_id)->where('user_id', Auth::id())->first();

        // Enforce Edit authority constraints: Sub Leaders can "Save" attendance but only Vice Leaders can override/edit after save.
        if ($meeting->status === 'completed' && $currentUserRole && $currentUserRole->role === 'sub_leader') {
            // Can't edit after it's been completed
            return back()->with('error', 'Attendance is locked. You can no longer modify it without Vice Leader approval.');
        }

        // 1. تصفير الحضور
        \App\Models\MeetingAttendance::where('meeting_id', $meeting->id)->update(['is_present' => false]);

        // 2. تسجيل الحضور للي اخترناهم
        if ($request->has('attendees')) {
            \App\Models\MeetingAttendance::where('meeting_id', $meeting->id)
                ->whereIn('user_id', $request->attendees)
                ->update(['is_present' => true]);
        }

        $meeting->update(['status' => 'completed']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance saved ✅',
                'redirect' => route('final_project.dashboard', $meeting->team_id) // Trigger refresh
            ]);
        }

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
            $storedPath = $request->file('image')->store('gallery', 'r2');
            $filePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Artifact uploaded successfully! 🚀',
                'redirect' => route('final_project.dashboard', $request->team_id) . '#gallery-section'
            ]);
        }

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

        $teamId = $item->team_id;
        DB::table('project_galleries')->where('id', $id)->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed.',
                'redirect' => route('final_project.dashboard', $teamId) . '#gallery-section'
            ]);
        }

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
            'submission_type' => 'required|in:file,link',

            // لو النوع ملف: يبقى الملف إجباري
            'submission_file' => 'required_if:submission_type,file|nullable|file|mimes:pdf,zip,rar,doc,docx,png,jpg|max:102400',

            // لو النوع لينك: يبقى اللينك إجباري
            'link' => 'required_if:submission_type,link|nullable|url',

            'submission_comment' => 'nullable|string|max:1000'
        ], [
            'submission_file.file' => 'The file could not be uploaded. It might be too large (exceeds PHP limits) or corrupted.',
            'submission_file.max' => 'The file size cannot exceed 100MB.'
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
                    
                    $storedPath = $file->store('submissions', 'r2');
                    $path = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);

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

            if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task submitted successfully!',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Task submitted successfully!');
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Task not found.'], 404);
        }
        return back()->with('error', 'Task not found.');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Task submission failed: ' . $e->getMessage());
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Submission failed. Please try again.'], 500);
        }
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

            if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task approved successfully! ✅',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Task approved successfully! ✅');
    } catch (\Exception $e) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Approval failed: ' . $e->getMessage()], 500);
        }
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

            if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task rejected ❌',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('error', 'Task rejected ❌');
    } catch (\Exception $e) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Rejection failed: ' . $e->getMessage()], 500);
        }
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

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Submission deleted. You can submit again.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back()->with('success', 'Submission deleted. You can submit again.');
    }

    // 21 دالة التسليم النهائي (الكتاب + الفيديو)
    public function submitFinalProject(Request $request)
    {
        $user = Auth::user();
        
        // 1. Get the member record for the current user
        // We assume the user belongs to only one team for this project context
        $memberRecord = TeamMember::where('user_id', $user->id)->first();
        
        if (!$memberRecord) {
            return back()->with('error', 'You are not a member of any team.');
        }

        $team = $memberRecord->team;
        $isLeader = $team->leader_id === $user->id;
        $extraRole = $memberRecord->extra_role ?? 'none';

        // 2. Authorization Check
        $canUploadPresentation = $isLeader || $extraRole === 'presentation';
        $canUploadBook = $isLeader || $extraRole === 'project_book';

        if (!$canUploadPresentation && !$canUploadBook) {
            return back()->with('error', 'You do not have permission to upload project files.');
        }

        // 3. Validation
        $request->validate([
            'final_book' => 'nullable|file|mimes:pdf|max:1048576',
            'presentation' => 'nullable|file|mimes:ppt,pptx,pdf|max:1048576',
            'defense_video' => 'nullable|url',
        ]);

        // 4. File Handling with Role Restrictions
        $updatedSomething = false;

        // Final Book: Leader or Project Book role
        if ($request->hasFile('final_book')) {
            if ($canUploadBook) {
                $storedPath = $request->file('final_book')->store('final_books', 'r2');
                $team->final_book_file = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
                $updatedSomething = true;
            } else {
                return back()->with('error', 'Only the Team Leader or Project Book Manager can upload the final book.');
            }
        }

        // Presentation: Leader or Presentation role
        if ($request->hasFile('presentation')) {
            if ($canUploadPresentation) {
                $storedPath = $request->file('presentation')->store('presentations', 'r2');
                $team->presentation_file = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
                $updatedSomething = true;
            } else {
                return back()->with('error', 'Only the Team Leader or Presentation Manager can upload the presentation.');
            }
        }

        // Video Link: Leader only (or as per general team consensus, but let's stick to Leader for core links)
        if ($request->defense_video) {
            if ($isLeader) {
                $team->defense_video_link = $request->defense_video;
                $updatedSomething = true;
            } else {
                // If they are not leader, we might skip or error. 
                // Let's just allow it if they have either upload role? No, let's keep it Leader for now unless requested.
            }
        }

        // 5. Final Submission (Locking the Project)
        if ($request->has('submit_finish')) {
            if ($isLeader) {
                $team->is_fully_submitted = true;
                $team->project_phase = 'completed';
                $team->submitted_at = now();
                $updatedSomething = true;
            } else {
                // Non-leaders CANNOT submit final. They only save drafts.
                return back()->with('warning', 'Draft saved. Only the Team Leader can perform the Final Submission.');
            }
        }

        if ($updatedSomething) {
            $team->save();
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project files updated successfully ✨',
                    'redirect' => back()->getTargetUrl()
                ]);
            }
            return back()->with('success', 'Project files updated successfully ✨');
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'No changes made.',
                'redirect' => back()->getTargetUrl()
            ]);
        }

        return back();
    }

    public function exportMembers(\Illuminate\Http\Request $request)
    {
        if (Auth::user()->role !== 'admin' && !Auth::user()->hasPermission('manage_users')) {
            abort(403, 'Unauthorized access to export feature.');
        }

        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'columns' => 'required|array|min:1',
            'technical_role' => 'nullable|string|in:all,software,hardware'
        ]);

        $technicalRole = $request->input('technical_role', 'all');
        $fileName = 'team_members' . ($technicalRole !== 'all' ? '_' . $technicalRole : '') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MembersExport($request->columns, $request->team_id, $technicalRole),
            $fileName
        );
    }
}
