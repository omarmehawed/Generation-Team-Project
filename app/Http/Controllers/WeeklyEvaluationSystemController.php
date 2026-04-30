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
        
        $myMember = TeamMember::where('user_id', '=', $userId)->where('team_id', '=', $team->id)->first();
        if (!$myMember) return redirect()->back();

        // 1. Determine Roles & Permissions
        $viewRole = $this->determineViewRole($myMember);

        // 2. Periods & Dates
        $allPeriods = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->orderBy('week_number', 'desc')->get();
        $currentPeriod = $this->resolveCurrentPeriod($request, $team);
        $startDate = $currentPeriod ? $currentPeriod->start_date->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate   = $currentPeriod ? ($currentPeriod->end_date ? $currentPeriod->end_date->endOfDay() : now()->endOfDay()) : now()->endOfDay();

        // 3. Members Filtering
        $allMembers = TeamMember::with('user')->where('team_id', '=', $team->id)->get();
        $visibility = $this->getFilteredMembers($allMembers, $myMember, $viewRole, $request);
        
        // 4. Workshops & Meetings
        $workshops = $this->getWorkshops($team, $viewRole, $startDate, $endDate);
        $meetings = $this->getMeetings($team, $viewRole, $myMember, $startDate, $endDate);

        // 5. Tasks
        $tasks = $this->getGroupedTasks($team, $viewRole, $visibility['allRegularMembers'], $startDate, $endDate);

        // 6. Stats & Records
        $completedCount = $currentPeriod ? WeeklyEvaluationRecord::where('evaluation_period_id', '=', $currentPeriod->id)->count() : 0;
        $stats = [
            'members_count' => $visibility['allRegularMembers']->count(),
            'sub_leaders_count' => $visibility['allSubLeaders']->count(),
            'pending_reviews' => max(0, ($visibility['allRegularMembers']->count() + $visibility['allSubLeaders']->count() + (($myMember->role === 'leader') ? $visibility['allViceLeaders']->count() : 0)) - $completedCount),
            'completed_this_week' => $completedCount,
        ];

        $existingRecords = $currentPeriod ? WeeklyEvaluationRecord::with('subItems')->where('evaluation_period_id', '=', $currentPeriod->id)->get()->keyBy('evaluatee_id') : collect();

        // Data for filters/modals
        $uniqueTeams = $this->getUniqueTeamNumbers($allMembers);
        $unassignedMembersFormatted = $this->formatUnassignedMembers($allMembers);
        $searchableMembers = $allMembers->where('user_id', '!=', $userId);

        return view('evaluation.index', array_merge($visibility, compact(
            'team', 'allPeriods', 'currentPeriod', 'workshops', 'tasks', 'meetings',
            'viewRole', 'stats', 'myMember', 'existingRecords', 'unassignedMembersFormatted', 'searchableMembers',
            'uniqueTeams'
        ), [
            'currentSearch' => $request->query('search'),
            'currentTeamFilter' => $request->query('team_filter'),
            'isLeader' => $myMember->role === 'leader',
            'isGeneralVice' => $viewRole === 'general_vice_leader',
            'isSoftwareVice' => $viewRole === 'software_vice_leader',
            'isHardwareVice' => $viewRole === 'hardware_vice_leader',
            'isSubLeader' => (bool)$myMember->is_sub_leader,
            'myRole' => $myMember->role,
            'allMembers' => $allMembers
        ]));
    }

    private function determineViewRole($myMember)
    {
        $role = $myMember->role;
        $tech = strtolower($myMember->technical_role ?? 'general');
        if ($role === 'leader') return 'leader';
        if ($role === 'vice_leader') return $tech === 'general' ? 'general_vice_leader' : ($tech . '_vice_leader');
        if ($myMember->is_sub_leader) return 'sub_leader';
        return 'member';
    }

    private function resolveCurrentPeriod($request, $team)
    {
        $periodId = $request->query('period_id');
        if ($periodId) return WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->find($periodId);
        return WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->where('status', '=', 'open')->first() 
               ?? WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->orderBy('week_number', 'desc')->first();
    }

    private function getFilteredMembers($allMembers, $myMember, $viewRole, $request)
    {
        $role = $myMember->role;
        $tech = strtolower($myMember->technical_role ?? 'general');

        if ($role === 'leader' || $viewRole === 'general_vice_leader') {
            $allViceLeaders = $allMembers->where('role', 'vice_leader');
            $allSubLeaders = $allMembers->where('is_sub_leader', true);
            $allRegularMembers = $allMembers->where('role', 'member')->where('is_sub_leader', false);
        } elseif ($viewRole === 'software_vice_leader' || $viewRole === 'hardware_vice_leader') {
            $domain = $viewRole === 'software_vice_leader' ? 'software' : 'hardware';
            $allViceLeaders = collect();
            $allSubLeaders = $allMembers->where('is_sub_leader', true)->filter(fn($m) => strtolower($m->technical_role) === $domain);
            $allRegularMembers = $allMembers->where('role', 'member')->where('is_sub_leader', false)->filter(fn($m) => strtolower($m->technical_role) === $domain);
        } elseif ($myMember->is_sub_leader) {
            $allViceLeaders = collect();
            $allSubLeaders = collect();
            $allRegularMembers = $allMembers->where('role', 'member')
                                 ->where('team_number', '=', $myMember->team_number)
                                 ->filter(fn($m) => strtolower($m->technical_role ?? 'general') === $tech)
                                 ->where('id', '!=', $myMember->id);
        } else {
            return ['allViceLeaders' => collect(), 'allSubLeaders' => collect(), 'allRegularMembers' => collect(), 'viceLeaders' => collect(), 'subLeaders' => collect(), 'members' => collect()];
        }

        // Search & Filter
        $search = $request->query('search');
        $teamFilter = $request->query('team_filter');
        if ($search || $teamFilter) {
            $closure = function($m) use ($search, $teamFilter) {
                if ($search) {
                    $q = strtolower($search);
                    if (!str_contains(strtolower($m->user->name ?? ''), $q) && !str_contains(strtolower($m->user->email ?? ''), $q)) return false;
                }
                if ($teamFilter) {
                    if (str_contains($teamFilter, '-')) {
                        [$num, $r] = explode('-', $teamFilter);
                        if ((string)$m->team_number !== (string)$num || strtolower($m->technical_role ?? 'general') !== strtolower($r)) return false;
                    } elseif ((string)$m->team_number !== (string)$teamFilter) return false;
                }
                return true;
            };
            $allRegularMembers = $allRegularMembers->filter($closure);
            $allSubLeaders = $allSubLeaders->filter($closure);
            $allViceLeaders = $allViceLeaders->filter($closure);
        }

        return [
            'allViceLeaders' => $allViceLeaders,
            'allSubLeaders' => $allSubLeaders,
            'allRegularMembers' => $allRegularMembers,
            'viceLeaders' => $this->paginateCollection($allViceLeaders, 10, 'viceleaders_page'),
            'subLeaders' => $this->paginateCollection($allSubLeaders, 10, 'subleaders_page'),
            'members' => $this->paginateCollection($allRegularMembers, 15, 'members_page'),
        ];
    }

    private function getWorkshops($team, $viewRole, $start, $end)
    {
        $query = \App\Models\Workshop::where('team_id', '=', $team->id)->whereBetween('workshop_date', [$start, $end])->orderBy('workshop_date', 'asc');
        $workshops = $query->get();
        if ($viewRole === 'software_vice_leader') $workshops = $workshops->filter(fn($w) => strtolower($w->technical_role ?? $w->domain) === 'software');
        if ($viewRole === 'hardware_vice_leader') $workshops = $workshops->filter(fn($w) => strtolower($w->technical_role ?? $w->domain) === 'hardware');
        return $this->paginateCollection($workshops, 10, 'workshops_page');
    }

    private function getMeetings($team, $viewRole, $myMember, $start, $end)
    {
        $q = \App\Models\Meeting::where('team_id', '=', $team->id)->whereBetween('meeting_date', [$start, $end]);
        if ($viewRole === 'software_vice_leader' || $viewRole === 'hardware_vice_leader') {
            $domain = ($viewRole === 'software_vice_leader') ? 'software' : 'hardware';
            $q->where(fn($sub) => $sub->whereNull('domain')->orWhere('domain', '=', $domain));
        } elseif ($viewRole === 'sub_leader' || $viewRole === 'member') {
            $tech = strtolower($myMember->technical_role ?? 'general');
            $q->where(fn($sub) => $sub->whereNull('domain')->orWhere('domain', '=', $tech))
              ->where(fn($sub) => $sub->whereNull('team_number')->orWhere('team_number', '=', $myMember->team_number));
        }
        return $q->get();
    }

    private function getGroupedTasks($team, $viewRole, $allRegularMembers, $start, $end)
    {
        $q = \App\Models\Task::with(['user.teamMemberships' => fn($m) => $m->where('team_id', '=', $team->id)])
            ->where('team_id', '=', $team->id)
            ->where(fn($sub) => $sub->whereBetween('deadline', [$start, $end])->orWhereBetween('submitted_at', [$start, $end])->orWhereBetween('created_at', [$start, $end]));
        
        if ($viewRole === 'software_vice_leader' || $viewRole === 'hardware_vice_leader') {
            $domain = ($viewRole === 'software_vice_leader') ? 'software' : 'hardware';
            $q->whereHas('user.teamMemberships', fn($sub) => $sub->where('team_id', '=', $team->id)->where('technical_role', '=', $domain));
        } elseif ($viewRole === 'sub_leader') {
            $q->whereIn('user_id', $allRegularMembers->pluck('user_id'));
        }

        $grouped = $q->get()->groupBy(function($t) use ($team) {
            $m = $t->user ? $t->user->teamMemberships->first() : null;
            return $t->title . '|' . ($t->deadline ? (is_string($t->deadline) ? $t->deadline : $t->deadline->toDateString()) : 'no-date') . '|' . strtolower($m->technical_role ?? 'general');
        })->map(function($group) {
            /** @var \Illuminate\Support\Collection $group */
            $first = $group->first();
            $m = ($first && $first->user) ? $first->user->teamMemberships->first() : null;
            return (object)[
                'title' => $first->title ?? 'Untitled Task',
                'deadline' => $first->deadline ?? null,
                'technical_role' => $m->technical_role ?? 'general',
                'status' => $group->every(fn($t) => $t->status === 'approved') ? 'approved' : ($group->contains(fn($t) => $t->status === 'rejected') ? 'rejected' : 'pending'),
                'members' => $group->map(fn($t) => $t->user->name ?? 'Unknown')->unique()->values(),
            ];
        })->values();

        return $this->paginateCollection($grouped, 10, 'tasks_page');
    }

    private function getUniqueTeamNumbers($members)
    {
        return $members->whereNotNull('team_number')->map(fn($m) => [
            'number' => $m->team_number, 'role' => strtolower($m->technical_role ?? 'general'), 'label' => 'Team ' . $m->team_number . ' ' . ucfirst($m->technical_role ?? 'General')
        ])->unique(fn($i) => $i['number'] . '-' . $i['role'])->sort(fn($a, $b) => ($a['number'] === $b['number']) ? strcmp($a['role'], $b['role']) : ($a['number'] <=> $b['number']))->values();
    }

    private function formatUnassignedMembers($members)
    {
        return $members->filter(fn($m) => is_null($m->team_number) && !$m->is_sub_leader && $m->role === 'member')->map(fn($m) => [
            'id' => $m->id, 'name' => $m->user->name ?? 'Unknown', 'email' => $m->user->email ?? '', 'technical_role' => strtolower($m->technical_role ?? 'general'),
        ])->values();
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

        $member = TeamMember::findOrFail($request->input('member_id'));
        
        if ($member->team_id != $team->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // ONE MEMBER, ONE SUB-TEAM RULE:
        if ($member->team_number && $member->team_number != $request->input('team_number')) {
            return back()->with('error', "This member is already assigned to Team #{$member->team_number}. They cannot belong to multiple teams.");
        }

        $member->update([
            'is_sub_leader' => true,
            'team_number' => $request->input('team_number'),
            'technical_role' => $request->input('technical_role'),
        ]);

        return back()->with('success', "Member '{$member->user->name}' assigned as {$request->input('technical_role')} Sub Leader for Team #{$request->input('team_number')}.");
    }

    public function assignMember(Request $request, Team $team)
    {
        $request->validate([
            'member_id' => 'required|exists:team_members,id',
            'team_number' => 'nullable|integer|min:1|max:20',
        ]);

        $userId = Auth::id();
        $myMember = TeamMember::where('team_id', '=', $team->id)->where('user_id', '=', $userId)->firstOrFail();
        
        // Determine the target team number
        $teamNumber = $request->input('team_number');
        if ($myMember->is_sub_leader) {
            $teamNumber = $myMember->team_number;
        }

        if (!$teamNumber) {
            return back()->with('error', 'Please select a team number for assignment.');
        }

        $member = TeamMember::where('team_id', '=', $team->id)->findOrFail($request->input('member_id'));
        
        // Find the sub-leader for this team number to set as parent_id
        $subLeader = TeamMember::where('team_id', '=', $team->id)
            ->where('team_number', '=', $teamNumber)
            ->where('is_sub_leader', '=', true)
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

        $member = TeamMember::where('team_id', '=', $team->id)->findOrFail($request->input('member_id'));
        
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
            $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->findOrFail($periodId);
        } else {
            $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->where('status', '=', 'open')->first()
                    ?? WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->orderBy('week_number', 'desc')->first();
        }

        if (!$period) {
            return back()->with('error', 'No evaluation period found. Please define a week first.');
        }

        $userId = Auth::id();
        $evaluator = TeamMember::where('team_id', '=', $team->id)->where('user_id', '=', $userId)->firstOrFail();

        $taskScore    = (float) ($request->input('task_score')    ?? 0);
        $workshopScore = (float) ($request->input('workshop_score') ?? 0);
        $meetingScore  = (float) ($request->input('meeting_score')  ?? 0);
        
        // Dynamic Denominator Calculation
        $hasTasks = (bool)$request->input('has_tasks');
        $hasWorkshops = (bool)$request->input('has_workshops');
        $hasMeetings = (bool)$request->input('has_meetings');
        
        $possibleScore = 0;
        if ($hasTasks) $possibleScore += 10;
        if ($hasWorkshops) $possibleScore += 10;
        if ($hasMeetings) $possibleScore += 10;
        
        // Baseline if nothing is assigned (fallback to 10 or 30?) 
        // User says: "if he had only Workshop → his total should be out of 10"
        $totalScore = $taskScore + $workshopScore + $meetingScore;
        
        $activeCategories = [];
        if ($hasTasks) $activeCategories[] = 'tasks';
        if ($hasWorkshops) $activeCategories[] = 'workshops';
        if ($hasMeetings) $activeCategories[] = 'meetings';
        $activeCategoriesStr = implode(',', $activeCategories);

        // Store the evaluation record
        $record = WeeklyEvaluationRecord::updateOrCreate(
            [
                'evaluation_period_id' => $period->id,
                'evaluatee_id' => $request->input('evaluatee_id'),
            ],
            [
                'evaluator_id'        => $evaluator->id,
                'total_task_score'    => $hasTasks ? $taskScore : 0,
                'total_workshop_score' => $hasWorkshops ? $workshopScore : 0,
                'total_meeting_score'  => $hasMeetings ? $meetingScore : 0,
                'total_overall_score' => $totalScore,
                'total_possible_score' => $possibleScore,
                'active_categories'   => $activeCategoriesStr,
                'general_notes'       => $request->input('general_notes'),
                'evaluation_type'     => $request->input('evaluation_type'),
            ]
        );

        // Store sub-items if provided
        if ($request->has('sub_items') && is_array($request->input('sub_items'))) {
            // Delete old sub items first
            WeeklyEvaluationSubItem::where('weekly_evaluation_record_id', '=', $record->id)->delete();
            foreach ($request->input('sub_items') as $item) {
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
        $myMember = TeamMember::where('team_id', '=', $team->id)->where('user_id', '=', $userId)->firstOrFail();

        if (!in_array($myMember->role, ['leader', 'vice_leader'])) {
            return back()->with('error', 'Unauthorized: Only Leaders and Vice Leaders can complete a week.');
        }

        $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->where('status', '=', 'open')->first();

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

    public function export(Request $request, Team $team)
    {
        $userId = Auth::id();
        $myMember = TeamMember::where('team_id', '=', $team->id)->where('user_id', '=', $userId)->firstOrFail();

        if (!in_array($myMember->role, ['leader', 'vice_leader'])) {
            return back()->with('error', 'Unauthorized.');
        }

        // Determine current period for context
        $periodId = $request->query('period_id');
        if ($periodId && $periodId !== 'null') {
            $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->find($periodId);
        } else {
            $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->where('status', '=', 'open')->first() 
                      ?? WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->orderBy('week_number', 'desc')->first();
        }

        if (!$period) {
            return back()->with('error', 'No evaluation period found to export.');
        }

        // 1. Fetch ALL members associated with this project
        $allMembers = TeamMember::with('user')->where('team_id', '=', $team->id)->get();

        // 2. Filter members based on role visibility (Matching the index logic)
        $myRole = $myMember->role;
        $technicalRole = strtolower($myMember->technical_role ?? 'general');
        $isLeader = $myRole === 'leader';
        $isGeneralVice = $myRole === 'vice_leader' && $technicalRole === 'general';
        $isSoftwareVice = $myRole === 'vice_leader' && $technicalRole === 'software';
        $isHardwareVice = $myRole === 'vice_leader' && $technicalRole === 'hardware';

        if ($isLeader || $isGeneralVice) {
            $membersToExport = $allMembers;
        } elseif ($isSoftwareVice || $isHardwareVice) {
            $domain = $isSoftwareVice ? 'software' : 'hardware';
            $membersToExport = $allMembers->filter(fn($m) => strtolower($m->technical_role) === $domain || $m->id === $myMember->id);
        } else {
            $membersToExport = collect([$myMember]);
        }

        // 3. Fetch existing records for this period to populate scores
        $records = WeeklyEvaluationRecord::where('evaluation_period_id', '=', $period->id)
            ->get()
            ->keyBy('evaluatee_id');

        $rows = [];
        $rows[] = ['Week', 'Member Name', 'Academic Number', 'Domain', 'Team #', 'Task (/10)', 'Workshop (/10)', 'Meeting (/10)', 'Overall Score', 'Notes', 'Evaluation Status'];

        foreach ($membersToExport as $member) {
            $user = $member->user;
            $academicNumber = ($user && $user->email) ? explode('@', $user->email)[0] : 'N/A';
            $record = $records->get($member->id);
            $active = $record ? explode(',', $record->active_categories ?? '') : [];

            $rows[] = [
                'Week ' . $period->week_number,
                $user->name ?? 'N/A',
                $academicNumber,
                ucfirst($member->technical_role ?? 'General'),
                $member->team_number ?? '-',
                ($record && in_array('tasks', $active)) ? number_format($record->total_task_score, 1) : '-',
                ($record && in_array('workshops', $active)) ? number_format($record->total_workshop_score, 1) : '-',
                ($record && in_array('meetings', $active)) ? number_format($record->total_meeting_score, 1) : '-',
                $record ? (number_format($record->total_overall_score, 1) . ' / ' . number_format($record->total_possible_score, 0)) : 'Pending',
                $record->general_notes ?? '',
                $record ? 'Evaluated' : 'Pending',
            ];
        }

        $filename = 'weekly_eval_W' . $period->week_number . '_' . str_replace(' ', '_', $team->name) . '_' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
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
        WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->where('status', '=', 'open')->update(['status' => 'closed']);

        WeeklyEvaluationPeriod::create([
            'team_id' => $team->id,
            'week_number' => $request->input('week_number'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => 'open',
        ]);

        return back()->with('success', "Week #{$request->week_number} defined successfully.");
    }

    public function autoScore(Team $team, $evaluateeId)
    {
        $periodId = request()->query('period_id');
        if ($periodId && $periodId !== 'null' && $periodId !== '') {
            $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->find($periodId);
        } else {
            $period = WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->where('status', '=', 'open')->first()
                   ?? WeeklyEvaluationPeriod::where('team_id', '=', $team->id)->orderBy('week_number', 'desc')->first();
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
        $tasks = \App\Models\Task::where('user_id', '=', $userId)
            ->where('team_id', '=', $team->id)
            ->where(function($q) use ($startDate, $endDate) {
                // STRICT FILTERING: Only tasks created or due in this week
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->orWhereBetween('deadline', [$startDate, $endDate]);
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
        $techRole = strtolower($member->technical_role ?? 'general');
        $workshops = \App\Models\Workshop::where('team_id', '=', $team->id)
            ->whereBetween('workshop_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($techRole !== 'general', function($q) use ($techRole) {
                return $q->where(function($sub) use ($techRole) {
                    $sub->where('domain', '=', 'general')->orWhere('domain', '=', $techRole);
                });
            })
            ->get();

        $workshopAttendances = \App\Models\WorkshopAttendee::where('user_id', '=', $userId)
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
        $meetings = \App\Models\Meeting::where('team_id', '=', $team->id)
            ->whereBetween('meeting_date', [$startDate, $endDate])
            ->get();

        $meetingAttendances = \App\Models\MeetingAttendance::where('user_id', '=', $userId)
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
            'has_tasks'                 => count($taskData) > 0,
            'has_workshops'             => count($workshopData) > 0,
            'has_meetings'              => count($meetingData) > 0,
            'suggested_task_score'      => count($taskData) > 0 ? (float)max(0, 10 - $taskPenalty) : 0,
            'suggested_workshop_score'  => count($workshopData) > 0 ? (float)round(collect($workshopData)->avg('score'), 1) : 0,
            'suggested_meeting_score'   => count($meetingData) > 0 ? (float)round(collect($meetingData)->avg('score'), 1) : 0,
            'total_penalty'             => $taskPenalty,
            'period_week'               => $period->week_number,
        ]);
    }
}


