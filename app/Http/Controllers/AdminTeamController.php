<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class AdminTeamController extends Controller
{
    public function index(Request $request)
    {
        // 1. تحميل العلاقات
        $query = Team::with(['leader', 'members.user', 'project.course', 'ta']);

        // 2.  السيرش المطور (شامل كل حاجة)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")       // اسم التيم
                    ->orWhere('code', 'LIKE', "%{$search}%")     //  كود التيم
                    ->orWhere('proposal_title', 'LIKE', "%{$search}%") //  اسم المشروع (للتخرج)

                    // اسم الليدر
                    ->orWhereHas('leader', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    })
                    // اسم أي عضو
                    ->orWhereHas('members.user', function ($q3) use ($search) {
                        $q3->where('name', 'LIKE', "%{$search}%");
                    })
                    //  اسم المادة (Subject Name)
                    ->orWhereHas('project.course', function ($q4) use ($search) {
                        $q4->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        //  (باقي الفلاتر زي ما هي: التاريخ والنوع)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('type')) {
            if ($request->type === 'graduation') {
                $query->whereNotNull('ta_id');
            } elseif ($request->type === 'subject') {
                $query->whereNull('ta_id');
            }
        }

        $teams = $query->latest()->paginate(20);

        return view('admin.teams&team_members_mange_DB', compact('teams'));
    }

    //  حذف عضو من التيم
    public function removeMember($team_id, $user_id)
    {
        // حذف العضو
        TeamMember::where('team_id', $team_id)
            ->where('user_id', $user_id)
            ->delete();

        return back()->with('success', 'Member removed from team successfully.');
    }

    //  حذف التيم بالكامل
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete(); // Soft Delete لو مفعلها، أو Hard Delete
        return back()->with('success', 'Team deleted successfully.');
    }
}
