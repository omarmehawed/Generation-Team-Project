<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopAttendee;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkshopController extends Controller
{
    // ==========================================
    // 1. Create Workshop
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'title' => 'required|string|max:255',
            'workshop_date' => 'required|date',
            'workshop_time' => 'required',
            'type' => 'required|in:online,offline',
            'location_or_link' => 'nullable|string',
            'domain' => 'required|in:software,hardware,general',
        ]);

        $team = Team::findOrFail($request->team_id);
        $myMemberRecord = $team->members()->where('user_id', Auth::id())->first();
        
        $myRole = $myMemberRecord->role ?? 'member';
        $myDomain = $myMemberRecord->technical_role ?? 'general';

        if (!in_array($myRole, ['leader', 'vice_leader'])) {
            abort(403, 'Only Leader or Vice Leader can create workshops.');
        }

        // Vice Leader domain restriction
        if ($myRole === 'vice_leader' && strtolower($request->domain) !== strtolower($myDomain)) {
            return back()->with('error', 'You can only create workshops for your domain (' . ucfirst($myDomain) . ').');
        }

        $workshop = Workshop::create([
            'team_id' => $team->id,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'workshop_date' => $request->workshop_date,
            'workshop_time' => $request->workshop_time,
            'type' => $request->type,
            'location_or_link' => $request->location_or_link,
            'domain' => strtolower($request->domain),
        ]);

        // Auto-assign attendees (all members of that domain, including Sub Leaders, but excluding Vice Leaders and Leaders if needed? Wait, usually we assign all members of that team)
        $domainMembers = TeamMember::where('team_id', $team->id)
            ->where('role', 'member')
            ->when($request->domain !== 'general', function ($query) use ($request) {
                return $query->where('technical_role', strtolower($request->domain));
            })->get();

        foreach ($domainMembers as $member) {
            WorkshopAttendee::create([
                'workshop_id' => $workshop->id,
                'user_id' => $member->user_id,
                'status' => 'pending',
                'participation_score' => 0
            ]);
        }

        return back()->with('success', 'Workshop created successfully and members assigned!');
    }

    // ==========================================
    // 2. View Workshop Details (Modal or Page)
    // ==========================================
    public function show($id)
    {
        $workshop = Workshop::with(['attendees.user', 'creator'])->findOrFail($id);
        return response()->json($workshop);
    }

    // ==========================================
    // 2b. Get Attendees as JSON (for modal search)
    // ==========================================
    public function getAttendees($id)
    {
        $workshop = Workshop::findOrFail($id);
        
        // Check if user is a Sub Leader
        $teamId = $workshop->team_id;
        $myMemberRecord = TeamMember::where('team_id', $teamId)->where('user_id', Auth::id())->first();
        
        $query = WorkshopAttendee::with(['user'])->where('workshop_id', $id);

        if ($myMemberRecord && $myMemberRecord->is_sub_leader && !in_array($myMemberRecord->role, ['leader', 'vice_leader'])) {
            // Sub Leader can only see their team members
            $query->whereHas('user.teamMemberships', function($q) use ($teamId, $myMemberRecord) {
                $q->where('team_id', $teamId)
                  ->where('parent_id', $myMemberRecord->id);
            });
        }

        $attendees = $query->get();

        $data = $attendees->map(function ($a) {
            return [
                'id'                  => $a->id,
                'user_id'             => $a->user_id,
                'name'                => $a->user->name ?? 'Unknown',
                'email'               => $a->user->email ?? '',
                'status'              => $a->status ?? 'pending',
                'participation_score' => (float) ($a->participation_score ?? 0),
            ];
        });

        return response()->json([
            'workshop_id'   => $workshop->id,
            'workshop_title'=> $workshop->title,
            'attendees'     => $data,
        ]);
    }

    // ==========================================
    // 3. Mark Attendance / Update Scores (by Sub Leader or Vice Leader)
    // ==========================================
    public function updateAttendance(Request $request, $id)
    {
        $workshop = Workshop::findOrFail($id);
        $teamId = $workshop->team_id;

        $myMemberRecord = TeamMember::where('team_id', $teamId)->where('user_id', Auth::id())->first();
        if (!$myMemberRecord || (!in_array($myMemberRecord->role, ['leader', 'vice_leader']) && !$myMemberRecord->is_sub_leader)) {
            abort(403, 'Unauthorized');
        }

        // Support JSON body (AJAX) or form body
        $input = $request->isJson() ? $request->json('attendees', []) : $request->input('attendees', []);

        foreach ($input as $data) {
            $attendee = WorkshopAttendee::find($data['id'] ?? null);
            if (!$attendee) continue;

            // Sub Leader scope check
            if ($myMemberRecord->is_sub_leader && !in_array($myMemberRecord->role, ['leader', 'vice_leader'])) {
                $attendeeMember = TeamMember::where('team_id', $teamId)->where('user_id', $attendee->user_id)->first();
                if (!$attendeeMember || $attendeeMember->parent_id !== $myMemberRecord->id) {
                    continue;
                }
            }

            $status = $data['status'] ?? 'pending';
            $score  = ($status === 'absent') ? 0 : (float) ($data['participation_score'] ?? 0);

            $attendee->update([
                'status'              => $status,
                'participation_score' => $score,
            ]);
        }

        if ($request->isJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Attendance & scores saved.']);
        }

        return back()->with('success', 'Attendance & Scores updated successfully.');
    }

    // ==========================================
    // 4. Delete Workshop
    // ==========================================
    public function destroy($id)
    {
        $workshop = Workshop::findOrFail($id);
        
        $myMemberRecord = TeamMember::where('team_id', $workshop->team_id)->where('user_id', Auth::id())->first();
        if (!$myMemberRecord || !in_array($myMemberRecord->role, ['leader', 'vice_leader'])) {
            abort(403, 'Unauthorized');
        }

        $workshop->delete();
        return back()->with('success', 'Workshop deleted successfully.');
    }
}
