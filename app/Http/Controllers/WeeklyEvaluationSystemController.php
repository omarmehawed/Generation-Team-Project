<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\WeeklyEvaluationPeriod;
use App\Models\WeeklyEvaluationRecord;
use App\Models\WeeklyEvaluationSubItem;
use Illuminate\Support\Facades\Auth;

class WeeklyEvaluationSystemController extends Controller
{
    public function index(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);
        $userId = Auth::id();
        if (!$userId) return redirect()->route('login');
        
        $myMember = TeamMember::where('user_id', $userId)->where('team_id', $team->id)->first();
        if (!$myMember) return redirect()->back();

        $myRole = $myMember->role;
        $technicalRole = strtolower($myMember->technical_role ?? 'general');
        $isSubLeader = $myMember->is_sub_leader;
        $isLeader = $myRole === 'leader';
        $isGeneralVice = $myRole === 'vice_leader' && $technicalRole === 'general';
        $isSoftwareVice = $myRole === 'vice_leader' && $technicalRole === 'software';
        $isHardwareVice = $myRole === 'vice_leader' && $technicalRole === 'hardware';

        // Map internal role names to the view's expected $viewRole
        if ($myRole === 'leader') {
            $viewRole = 'leader';
        } elseif ($myRole === 'vice_leader') {
            $viewRole = $technicalRole === 'general' ? 'general_vice_leader' : ($technicalRole . '_vice_leader');
        } elseif ($isSubLeader) {
            $viewRole = 'sub_leader';
        } else {
            $viewRole = 'member';
        }

        // Shared Data: Periods
        $allPeriods = WeeklyEvaluationPeriod::where('team_id', $team->id)->orderBy('week_number', 'desc')->get();
        
        // Determine Current Period
        $periodId = $request->query('period_id');
        if ($periodId) {
            $currentPeriod = WeeklyEvaluationPeriod::where('team_id', $team->id)->find($periodId);
        } else {
            $currentPeriod = WeeklyEvaluationPeriod::where('team_id', $team->id)->where('status', 'open')->first() 
                            ?? WeeklyEvaluationPeriod::where('team_id', $team->id)->orderBy('week_number', 'desc')->first();
        }

        // Dashboard Date Range
        $startDate = $currentPeriod ? $currentPeriod->start_date->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate   = $currentPeriod ? ($currentPeriod->end_date ? $currentPeriod->end_date->endOfDay() : now()->endOfDay()) : now()->endOfDay();

        // 1. Fetch ALL relevant items
        $allMembers = TeamMember::with('user')->where('team_id', $team->id)->get();

        // Unique team numbers for the filter dropdown
        $uniqueTeamNumbers = $allMembers->whereNotNull('team_number')->pluck('team_number')->unique()->sort()->values();

        // 2. Filter Lists based on Role Visibility Rules
        if ($isLeader) {
            $allViceLeaders = $allMembers->where('role', 'vice_leader');
            $allSubLeaders = $allMembers->where('is_sub_leader', true);
            $allRegularMembers = $allMembers->where('role', 'member')->where('is_sub_leader', false);
        } elseif ($isGeneralVice) {
            $allViceLeaders = collect();
            $allSubLeaders = $allMembers->where('is_sub_leader', true);
            $allRegularMembers = $allMembers->where('role', 'member')->where('is_sub_leader', false);
        } elseif ($isSoftwareVice || $isHardwareVice) {
            $domain = $isSoftwareVice ? 'software' : 'hardware';
            $allViceLeaders = collect();
            $allSubLeaders = $allMembers->where('is_sub_leader', true)->filter(fn($m) => strtolower($m->technical_role) === $domain);
            $allRegularMembers = $allMembers->where('role', 'member')->where('is_sub_leader', false)->filter(fn($m) => strtolower($m->technical_role) === $domain);
        } elseif ($isSubLeader) {
            $allViceLeaders = collect();
            $allSubLeaders = collect();
            $allRegularMembers = $allMembers->where('role', 'member')
                                 ->where('team_number', $myMember->team_number)
                                 ->where('id', '!=', $myMember->id);
        } else {
            $allViceLeaders = collect();
            $allSubLeaders = collect();
            $allRegularMembers = collect();
        }

        // Apply Search & Team Filter
        $currentSearch = $request->query('search');
        $currentTeamFilter = $request->query('team_filter');

        $filterClosure = function($m) use ($currentSearch, $currentTeamFilter) {
            $match = true;
            if ($currentSearch) {
                $q = strtolower($currentSearch);
                $userName = strtolower($m->user->name ?? '');
                $userEmail = strtolower($m->user->email ?? '');
                $userUniEmail = strtolower($m->user->university_email ?? '');
                
                $match = str_contains($userName, $q) || 
                         str_contains($userEmail, $q) || 
                         str_contains($userUniEmail, $q);
            }
            if ($match && $currentTeamFilter) {
                $match = (string)$m->team_number === (string)$currentTeamFilter;
            }
            return $match;
        };

        if ($currentSearch || $currentTeamFilter) {
            $allRegularMembers = $allRegularMembers->filter($filterClosure);
            $allSubLeaders = $allSubLeaders->filter($filterClosure);
            $allViceLeaders = $allViceLeaders->filter($filterClosure);
        }

        // Final filtering for Vice/Sub/Members based on domain if needed
        if ($viewRole === 'software_vice_leader') {
            $allSubLeaders = $allSubLeaders->filter(fn($m) => strtolower($m->technical_role) === 'software');
            $allRegularMembers = $allRegularMembers->filter(fn($m) => strtolower($m->technical_role) === 'software');
        } elseif ($viewRole === 'hardware_vice_leader') {
            $allSubLeaders = $allSubLeaders->filter(fn($m) => strtolower($m->technical_role) === 'hardware');
            $allRegularMembers = $allRegularMembers->filter(fn($m) => strtolower($m->technical_role) === 'hardware');
        }

        // 3. Paginate Collections
        $members = $this->paginateCollection($allRegularMembers, 15, 'members_page');
        $subLeaders = $this->paginateCollection($allSubLeaders, 10, 'subleaders_page');
        $viceLeaders = $this->paginateCollection($allViceLeaders, 10, 'viceleaders_page');

        // Other filters
        $searchableMembers = $allMembers->where('user_id', '!=', $userId);
        $unassignedMembers = $allMembers->filter(fn($m) => is_null($m->team_number) && !$m->is_sub_leader && $m->role === 'member');
        $unassignedMembersFormatted = $unassignedMembers->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->user->name ?? 'Unknown',
            'email' => $m->user->email ?? '',
            'academic_number' => explode('@', $m->user->email ?? '')[0] ?? '',
            'avatar' => ($m->user?->profile_photo_url) ?? 'https://ui-avatars.com/api/?name=' . urlencode($m->user->name ?? 'U') . '&background=6366f1&color=fff&bold=true',
            'technical_role' => strtolower($m->technical_role ?? 'general'),
            'member_tech' => strtoupper($m->technical_role ?? 'general'),
        ])->values();

        // 4. Workshops Pagination
        $allWorkshops = \App\Models\Workshop::where('team_id', $team->id)
            ->whereBetween('workshop_date', [$startDate, $endDate])
            ->orderBy('workshop_date', 'asc')
            ->get();
        if ($viewRole === 'software_vice_leader') {
            $allWorkshops = $allWorkshops->filter(fn($w) => strtolower($w->technical_role ?? $w->domain) === 'software');
        } elseif ($viewRole === 'hardware_vice_leader') {
            $allWorkshops = $allWorkshops->filter(fn($w) => strtolower($w->technical_role ?? $w->domain) === 'hardware');
        }
        $workshops = $this->paginateCollection($allWorkshops, 10, 'workshops_page');

        // 5. Meetings
        $meetingsQuery = \App\Models\Meeting::where('team_id', $team->id)
            ->whereBetween('meeting_date', [$startDate, $endDate]);
        if ($viewRole === 'software_vice_leader' || $viewRole === 'hardware_vice_leader') {
            $domainType = $viewRole === 'software_vice_leader' ? 'software' : 'hardware';
            $meetingsQuery->where(function($q) use ($domainType) { $q->whereNull('domain')->orWhere('domain', $domainType); });
        } elseif ($viewRole === 'sub_leader' || $viewRole === 'member') {
            $meetingsQuery->where(function($q) use ($myMember, $technicalRole) {
                $q->whereNull('domain')->whereNull('team_number')
                  ->orWhere(function($sub) use ($technicalRole) { $sub->where('domain', $technicalRole)->whereNull('team_number'); });
                if ($myMember->team_number) { $q->orWhere('team_number', $myMember->team_number); }
            });
        }
        $meetings = $meetingsQuery->get();

        // 6. Tasks (Grouped logic + Pagination)
        $tasksQuery = \App\Models\Task::with('user')->where('team_id', $team->id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('deadline', [$startDate, $endDate])
                  ->orWhereBetween('submitted_at', [$startDate, $endDate])
                  ->orWhereBetween('created_at', [$startDate, $endDate]);
            });
        
        if ($viewRole === 'software_vice_leader') {
            $tasksQuery->where('technical_role', 'software');
        } elseif ($viewRole === 'hardware_vice_leader') {
            $tasksQuery->where('technical_role', 'hardware');
        } elseif ($viewRole === 'sub_leader') {
             // For sub-leaders, we only show tasks related to their team members
             $myTeamIds = $allRegularMembers->pluck('user_id');
             $tasksQuery->whereIn('user_id', $myTeamIds);
        }
        
        $rawTasks = $tasksQuery->get();
        
        $groupedTasksCollection = $rawTasks->groupBy(function($t) {
            return $t->title . '|' . ($t->deadline ? (is_string($t->deadline) ? $t->deadline : $t->deadline->toDateString()) : 'no-date') . '|' . strtolower($t->technical_role);
        })->map(function($group) {
            $first = $group->first();
            return (object)[
                'title' => $first->title,
                'deadline' => $first->deadline,
                'technical_role' => $first->technical_role,
                'status' => $group->every(fn($t) => $t->status === 'approved') ? 'approved' : 
                           ($group->contains(fn($t) => $t->status === 'rejected') ? 'rejected' : 'pending'),
                'members' => $group->map(fn($t) => $t->user->name ?? 'Unknown')->unique()->values(),
            ];
        })->values();

        $tasks = $this->paginateCollection($groupedTasksCollection, 10, 'tasks_page');

        // 7. Stats calculation
        $completedCount = $currentPeriod ? WeeklyEvaluationRecord::where('evaluation_period_id', $currentPeriod->id)->count() : 0;
        $stats = [
            'members_count' => $allRegularMembers->count(),
            'sub_leaders_count' => $allSubLeaders->count(),
            'pending_reviews' => max(0, ($allRegularMembers->count() + $allSubLeaders->count() + ($viewRole === 'leader' ? $allViceLeaders->count() : 0)) - $completedCount),
            'completed_this_week' => $completedCount,
        ];

        $softwareMembers = $allMembers->filter(fn($m) => strtolower($m->technical_role) === 'software');
        $hardwareMembers = $allMembers->filter(fn($m) => strtolower($m->technical_role) === 'hardware');
        $softwareStats = [
            'members_count' => $softwareMembers->where('role', 'member')->where('is_sub_leader', false)->count(),
            'sub_leaders_count' => $softwareMembers->where('is_sub_leader', true)->count(),
        ];
        $hardwareStats = [
            'members_count' => $hardwareMembers->where('role', 'member')->where('is_sub_leader', false)->count(),
            'sub_leaders_count' => $hardwareMembers->where('is_sub_leader', true)->count(),
        ];

        // 8. Existing Records
        $existingRecords = collect();
        if ($currentPeriod) {
            $existingRecords = WeeklyEvaluationRecord::with('subItems')
                ->where('evaluation_period_id', $currentPeriod->id)
                ->get()
                ->keyBy('evaluatee_id');
        }

        $isLeader = $myRole === 'leader';
        $isGeneralVice = $viewRole === 'general_vice_leader';

        return view('evaluation.index', compact(
            'team', 'allPeriods', 'currentPeriod', 'members', 'subLeaders', 'viceLeaders', 'workshops', 'tasks', 'meetings',
            'viewRole', 'stats', 'softwareStats', 'hardwareStats', 'isLeader', 'isGeneralVice', 'isSoftwareVice', 'isHardwareVice',
            'isSubLeader', 'myMember', 'myRole', 'allMembers', 'existingRecords', 'unassignedMembersFormatted', 'searchableMembers',
            'uniqueTeamNumbers', 'currentSearch', 'currentTeamFilter'
        ));
    }

    private function paginateCollection($items, $perPage, $pageName)
    {
        $page = request()->get($pageName, 1);
        $offset = ($page * $perPage) - $perPage;
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => $pageName]
        );
    }

    public function assignSubLeader(Request $request, Team $team)
    {
        $request->validate([
            'member_id' => 'required|exists:team_members,id',
            'team_number' => 'required|integer|min:1|max:20',
            'technical_role' => 'required|string|in:software,hardware',
        ], [
            'team_number.required' => 'Please select a team number.',
            'technical_role.required' => 'The technical domain (Software/Hardware) is required.',
        ]);

        $member = TeamMember::findOrFail($request->member_id);
        
        if ($member->team_id != $team->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // ONE MEMBER, ONE SUB-TEAM RULE:
        if ($member->team_number && $member->team_number != $request->team_number) {
            return back()->with('error', "This member is already assigned to Team #{$member->team_number}. They cannot belong to multiple teams.");
        }

        $member->update([
            'is_sub_leader' => true,
            'team_number' => $request->team_number,
            'technical_role' => $request->technical_role,
        ]);

        return back()->with('success', "Member '{$member->user->name}' assigned as {$request->technical_role} Sub Leader for Team #{$request->team_number}.");
    }

    public function assignMember(Request $request, Team $team)
    {
        $request->validate([
            'member_id' => 'required|exists:team_members,id',
            'team_number' => 'nullable|integer|min:1|max:20',
        ]);

        $userId = Auth::id();
        $myMember = TeamMember::where('team_id', $team->id)->where('user_id', $userId)->firstOrFail();
        
        // Determine the target team number
        $teamNumber = $request->team_number;
        if ($myMember->is_sub_leader) {
            $teamNumber = $myMember->team_number;
        }

        if (!$teamNumber) {
            return back()->with('error', 'Please select a team number for assignment.');
        }

        $member = TeamMember::where('team_id', $team->id)->findOrFail($request->member_id);
        
        // Find the sub-leader for this team number to set as parent_id
        $subLeader = TeamMember::where('team_id', $team->id)
            ->where('team_number', $teamNumber)
            ->where('is_sub_leader', true)
            ->first();

        $member->update([
            'team_number' => $teamNumber,
            'parent_id' => $subLeader?->id,
        ]);

        return back()->with('success', "Member '{$member->user->name}' assignment updated to Team #{$teamNumber}.");
    }

    public function removeSubLeader(Request $request, Team $team)
    {
        $request->validate([
            'member_id' => 'required|exists:team_members,id',
        ]);

        $member = TeamMember::where('team_id', $team->id)->findOrFail($request->member_id);
        
        if ($member->role !== 'sub_leader' && !$member->is_sub_leader) {
            return back()->with('error', 'This member is not a sub-leader.');
        }

        $member->update([
            'role' => 'member',
            'is_sub_leader' => false
        ]);

        return back()->with('success', "Sub-leader '{$member->user->name}' removed and returned to normal member status.");
    }

    public function store(Request $request, Team $team)
    {
        // Find OR CREATE the selected evaluation period
        $periodId = $request->query('period_id');
        if ($periodId) {
            $period = WeeklyEvaluationPeriod::where('team_id', $team->id)->findOrFail($periodId);
        } else {
             $period = WeeklyEvaluationPeriod::where('team_id', $team->id)->where('status', 'open')->first()
                    ?? WeeklyEvaluationPeriod::where('team_id', $team->id)->orderBy('week_number', 'desc')->first();
        }

        if (!$period) {
            return back()->with('error', 'No evaluation period found. Please define a week first.');
        }

        $userId = Auth::id();
        $evaluator = TeamMember::where('team_id', $team->id)->where('user_id', $userId)->firstOrFail();

        $taskScore    = (float) ($request->task_score    ?? 0);
        $workshopScore = (float) ($request->workshop_score ?? 0);
        $meetingScore  = (float) ($request->meeting_score  ?? 0);
        $totalScore    = $taskScore + $workshopScore + $meetingScore;

        // Store the evaluation record
        $record = WeeklyEvaluationRecord::updateOrCreate(
            [
                'evaluation_period_id' => $period->id,
                'evaluatee_id' => $request->evaluatee_id,
            ],
            [
                'evaluator_id'        => $evaluator->id,
                'total_task_score'    => $taskScore,
                'total_workshop_score' => $workshopScore,
                'total_meeting_score'  => $meetingScore,
                'total_overall_score' => $totalScore,
                'general_notes'       => $request->general_notes,
                'evaluation_type'     => $request->evaluation_type,
            ]
        );

        // Store sub-items if provided
        if ($request->has('sub_items') && is_array($request->sub_items)) {
            // Delete old sub items first
            WeeklyEvaluationSubItem::where('weekly_evaluation_record_id', $record->id)->delete();
            foreach ($request->sub_items as $item) {
                WeeklyEvaluationSubItem::create([
                    'weekly_evaluation_record_id' => $record->id,
                    'source_type' => $item['type'],
                    'source_id'   => $item['id'],
                    'score'       => $item['score'] ?? 0,
                    'notes'       => $item['notes'] ?? null,
                ]);
            }
        }

        return back()->with('success', 'Evaluation submitted successfully.');
    }

    public function completeWeek(Request $request, Team $team)
    {
        $userId = Auth::id();
        $myMember = TeamMember::where('team_id', $team->id)->where('user_id', $userId)->firstOrFail();

        if (!in_array($myMember->role, ['leader', 'vice_leader'])) {
            return back()->with('error', 'Unauthorized: Only Leaders and Vice Leaders can complete a week.');
        }

        $period = WeeklyEvaluationPeriod::where('team_id', $team->id)->where('status', 'open')->first();

        if (!$period) {
            return back()->with('info', 'No open evaluation period found.');
        }

        $period->update([
            'status'       => 'closed',
            'end_date'     => now()->toDateString(),
            'completed_at' => now(),
        ]);

        return back()->with('success', "Week #{$period->week_number} has been finalized. A new week will start on next evaluation.");
    }

    public function export(Team $team)
    {
        $userId = Auth::id();
        $myMember = TeamMember::where('team_id', $team->id)->where('user_id', $userId)->firstOrFail();

        if (!in_array($myMember->role, ['leader', 'vice_leader'])) {
            return back()->with('error', 'Unauthorized.');
        }

        $periods = WeeklyEvaluationPeriod::where('team_id', $team->id)->with(['records.evaluatee.user'])->get();

        $rows = [];
        $rows[] = ['Week', 'Member Name', 'Academic Number', 'Domain', 'Task Score (/10)', 'Workshop Score (/10)', 'Meeting Score (/10)', 'Total (/30)', 'Notes'];

        foreach ($periods as $period) {
            foreach ($period->records as $record) {
                $member = $record->evaluatee;
                $user = $member->user;
                $academicNumber = ($user && $user->email) ? explode('@', $user->email)[0] : 'N/A';

                $rows[] = [
                    'Week ' . $period->week_number,
                    $user->name ?? 'N/A',
                    $academicNumber,
                    ucfirst($member->technical_role ?? 'General'),
                    number_format($record->total_task_score, 1),
                    number_format($record->total_workshop_score, 1),
                    number_format($record->total_meeting_score, 1),
                    number_format($record->total_overall_score, 1),
                    $record->general_notes ?? '',
                ];
            }
        }

        $filename = 'weekly_evaluation_' . $team->name . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storePeriod(Request $request, Team $team)
    {
        $request->validate([
            'week_number' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Close all other open periods for this team
        WeeklyEvaluationPeriod::where('team_id', $team->id)->where('status', 'open')->update(['status' => 'closed']);

        WeeklyEvaluationPeriod::create([
            'team_id' => $team->id,
            'week_number' => $request->week_number,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'open',
        ]);

        return back()->with('success', "Week #{$request->week_number} defined successfully.");
    }

    public function autoScore(Team $team, $evaluateeId)
    {
        $periodId = request()->query('period_id');
        if ($periodId && $periodId !== 'null' && $periodId !== '') {
            $period = WeeklyEvaluationPeriod::where('team_id', $team->id)->find($periodId);
        } else {
            $period = WeeklyEvaluationPeriod::where('team_id', $team->id)->where('status', 'open')->first()
                   ?? WeeklyEvaluationPeriod::where('team_id', $team->id)->orderBy('week_number', 'desc')->first();
        }

        if (!$period) return response()->json(['error' => 'No period found'], 404);

        // Define the week range
        $startDate = \Carbon\Carbon::parse($period->start_date)->startOfDay();
        $endDate   = \Carbon\Carbon::parse($period->end_date ?? now())->endOfDay();

        // 1. IDENTITY MAPPING
        $member = TeamMember::with('user')->findOrFail($evaluateeId);
        $userId = $member->user_id;

        // 2. COLLECT TASKS: Everything relevant to the user in this team
        // We include: 
        // - Tasks assigned during this week
        // - Tasks due during this week
        // - Tasks submitted/updated/approved during this week
        // - AND ANY unfinished tasks assigned before this week (ongoing work)
        $tasks = \App\Models\Task::where('user_id', $userId)
            ->where('team_id', $team->id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->orWhereBetween('deadline', [$startDate, $endDate])
                  ->orWhereBetween('submitted_at', [$startDate, $endDate])
                  ->orWhereBetween('updated_at', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($endDate) {
                      $sub->where('created_at', '<=', $endDate)
                          ->whereNotIn('status', ['completed', 'approved']);
                  });
            })
            ->get();

        $taskPenalty = 0;
        $taskData = [];
        foreach ($tasks as $t) {
            // Late: Submitted after deadline (regardless of when)
            $isLate = $t->submitted_at && $t->submitted_at > $t->deadline;
            
            // Missing: Not submitted yet, and deadline has passed (now > deadline)
            // Or if evaluating a historical week: (weekEnd > deadline)
            $referenceTime = now(); // For current evaluations
            if ($endDate < now()) $referenceTime = $endDate; // For historical evaluations
            
            $isMissing = !$t->submitted_at && $referenceTime > $t->deadline && !in_array($t->status, ['completed', 'approved']);
            
            if ($isLate || $isMissing) {
                $taskPenalty += 2;
            }

            $taskData[] = [
                'id'               => $t->id,
                'title'            => $t->title,
                'status'           => $t->status,
                'is_late'          => (bool)$isLate,
                'is_missing'       => (bool)$isMissing,
                'submission_type'  => $t->submission_type,
                'submission_file'  => $t->submission_file,
                'submission_value' => $t->submission_value,
            ];
        }

        // 3. COLLECT WORKSHOPS
        $workshops = \App\Models\Workshop::where('team_id', $team->id)
            ->whereBetween('workshop_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $workshopAttendances = \App\Models\WorkshopAttendee::where('user_id', $userId)
            ->whereIn('workshop_id', $workshops->pluck('id'))
            ->get()
            ->keyBy('workshop_id');

        $workshopData = [];
        foreach ($workshops as $w) {
            $att = $workshopAttendances->get($w->id);
            $workshopData[] = [
                'id'     => $w->id,
                'title'  => $w->title,
                'status' => $att ? $att->status : 'absent',
                'score'  => $att ? $att->participation_score : 0,
            ];
        }

        // 4. COLLECT MEETINGS: All team meetings in that week
        $meetings = \App\Models\Meeting::where('team_id', $team->id)
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->get();

        $meetingAttendances = \App\Models\MeetingAttendance::where('user_id', $userId)
            ->whereIn('meeting_id', $meetings->pluck('id'))
            ->get()
            ->keyBy('meeting_id');

        $meetingData = [];
        foreach ($meetings as $m) {
            $att = $meetingAttendances->get($m->id);
            $meetingData[] = [
                'id'     => $m->id,
                'topic'  => $m->topic ?? 'Meeting',
                'status' => $att && $att->is_present ? 'attended' : 'absent',
                'score'  => $att && $att->is_present ? 10 : 0,
            ];
        }

        return response()->json([
            'tasks'                     => $taskData,
            'workshops'                 => $workshopData,
            'meetings'                  => $meetingData,
            'suggested_task_score'      => (float)max(0, 10 - $taskPenalty),
            'suggested_workshop_score'  => count($workshopData) > 0 ? (float)round(collect($workshopData)->avg('score'), 1) : 0,
            'suggested_meeting_score'   => count($meetingData) > 0 ? (float)round(collect($meetingData)->avg('score'), 1) : 0,
            'total_penalty'             => $taskPenalty,
            'period_week'               => $period->week_number,
        ]);
    }
}


