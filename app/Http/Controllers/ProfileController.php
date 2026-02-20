<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Notifications\BatuNotification;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    /**
     * Display the user's detailed profile.
     */
    public function show($id = null)
    {
        $user = $id ? \App\Models\User::findOrFail($id) : Auth::user();

        // 1. Fetch Graduation Project Data
        // 1. Fetch Graduation Project Data
        $gradTeamMember = \App\Models\TeamMember::where('user_id', $user->id)
            ->whereHas('team.project', function($q) {
                // Determine if it's 'final' or 'graduation'
                $q->whereIn('type', ['final', 'graduation']);
            })
            ->with(['team.project', 'team.leader'])
            ->first();

        $gradProjectData = null;
        if ($gradTeamMember) {
            $gradProjectData = $this->getProjectAssets($gradTeamMember->team, $user);
        }

        // 2. Fetch Subject Projects Data
        $subjectTeamMembers = \App\Models\TeamMember::where('user_id', $user->id)
            ->whereHas('team.project', function($q) {
                $q->where('type', 'subject');
            })
            ->with(['team.project', 'team.leader'])
            ->get();
        
        $subjectProjectsData = [];
        foreach ($subjectTeamMembers as $member) {
            $subjectProjectsData[] = $this->getProjectAssets($member->team, $user);
        }

        return view('profile.show', [
            'user' => $user,
            'gradProjectData' => $gradProjectData,
            'subjectProjectsData' => $subjectProjectsData
        ]);
    }

    /**
     * Helper to gather all assets for a specific project team.
     */
    private function getProjectAssets($team, $user)
    {
        // A. Tasks (Assigned to User OR Team-wide)
        $tasks = \App\Models\Task::where('team_id', $team->id)
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereNull('user_id');
            })
            ->with(['grader', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // B. Reports (If Leader or Report Manager)
        $reports = [];
        $isLeader = $team->leader_id == $user->id;
        $memberRecord = $team->members()->where('user_id', $user->id)->first();
        $isReportManager = ($memberRecord->extra_role ?? '') == 'reports';

        if ($isLeader || $isReportManager) {
            $reports = \Illuminate\Support\Facades\DB::table('weekly_reports')
                ->where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->orderBy('week_number', 'desc')
                ->get();
        }

        // C. Meetings (Attended or Created)
        // 1. Where I am an attendee
        $attendedMeetingIds = \App\Models\MeetingAttendance::where('user_id', $user->id)
            ->pluck('meeting_id');
        
        $meetings = \App\Models\Meeting::where('team_id', $team->id)
            ->where(function($q) use ($attendedMeetingIds, $user, $isLeader) {
                $q->whereIn('id', $attendedMeetingIds);
                if($isLeader) {
                    $q->orWhere('type', 'supervision'); // Leaders see all supervision requests
                }
            })
            ->orderBy('meeting_date', 'desc')
            ->get();

        // D. Gallery (Uploaded by User)
        $gallery = \Illuminate\Support\Facades\DB::table('project_galleries')
            ->where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // E. Documents (Project Book, Authored Proposals, etc.)
        $docs = [];
        
        // Final Book / Presentation (Team Level - All can see)
        if ($team->final_book_file) {
            $docs[] = [
                'type' => 'Final Book',
                'name' => 'Final Project Book.pdf',
                'path' => $team->final_book_file,
                'icon' => 'fas fa-book'
            ];
        }
        if ($team->presentation_file) {
            $docs[] = [
                'type' => 'Presentation',
                'name' => 'Project Presentation.pptx',
                'path' => $team->presentation_file,
                'icon' => 'fas fa-chalkboard-teacher'
            ];
        }

        // Proposals (If Leader)
        if ($isLeader) {
            // Assuming proposals are stored or tracked. If they are just a status, we might not have a file.
            // But if we had a file path in a 'proposals' table or similar:
            // For now, we rely on the `project_phase` or similar if no direct file.
        }

        // F. Team Activities (Strict Filtering for this Team)
        $activities = ActivityLog::where('team_id', $team->id)
            ->where(function ($query) use ($user) {
                // A. I did the action
                $query->where('causer_id', $user->id);
                
                // B. Action was done TO me
                $query->orWhere('target_user_id', $user->id);

                // C. Team Broadcasts
                $query->orWhereIn('action', ['proposal_submitted', 'proposal_accepted', 'team_created']);
            })
            ->with(['causer'])
            ->latest()
            ->take(20)
            ->get();

        return [
            'project' => $team->project,
            'team' => $team,
            'assets' => [
                'tasks' => $tasks,
                'reports' => $reports,
                'meetings' => $meetings,
                'gallery' => $gallery,
                'docs' => $docs,
                'activities' => $activities // Added activities
            ]
        ];

    }

    /**
     * Update user's photo and phone.
     */
    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'phone_number' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|size:14|unique:users,national_id,' . $user->id,
            'address' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', 
        ]);

        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

        if ($request->has('national_id')) {
            $user->national_id = $request->national_id;
        }

        if ($request->has('address')) {
            $user->address = $request->address;
        }

        if ($request->hasFile('profile_photo')) {
            $path = Cloudinary::upload($request->file('profile_photo')->getRealPath(), ['folder' => 'profile-photos'])->getSecurePath();
            $user->profile_photo_path = $path;
        }

        $user->save();

        return back()->with('success', 'Profile details updated successfully!');
    }
}
