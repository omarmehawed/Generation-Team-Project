<?php

namespace App\Http\Controllers;

use App\Models\JoinRequestQuestion;
use App\Models\QuestionArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinRequestQuestionController extends Controller
{
    public function index()
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $questions = JoinRequestQuestion::with('archive')->orderBy('order_priority')->get();
        $archives = QuestionArchive::all();
        $success_message = \App\Models\Setting::get('join_request_success_message', 'Application Submitted! We have received your request.');
        return view('join_requests.questions.index', compact('questions', 'archives', 'success_message'));
    }

    public function showArchive($id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $activeArchive = QuestionArchive::findOrFail($id);
        $questions = JoinRequestQuestion::with('archive')->where('archive_id', $id)->orderBy('order_priority')->get();
        $archives = QuestionArchive::all();
        $success_message = \App\Models\Setting::get('join_request_success_message', 'Application Submitted! We have received your request.');
        return view('join_requests.questions.index', compact('questions', 'archives', 'success_message', 'activeArchive'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'section' => 'nullable|string',
            'archive_id' => 'nullable|exists:question_archives,id',
            'conditional_logic' => 'nullable|array',
        ]);

        $maxOrder = JoinRequestQuestion::max('order_priority') ?? 0;
        $validated['order_priority'] = $maxOrder + 1;

        JoinRequestQuestion::create($validated);

        return back()->with('success', 'Question added successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $question = JoinRequestQuestion::findOrFail($id);
        return response()->json($question);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $question = JoinRequestQuestion::findOrFail($id);
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'section' => 'string',
            'archive_id' => 'nullable|exists:question_archives,id',
            'conditional_logic' => 'nullable|array',
        ]);

        $question->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Question updated successfully.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $question = JoinRequestQuestion::findOrFail($id);
        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    public function reorder(Request $request)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $orders = $request->input('orders'); // Array of {id, order}
        
        foreach ($orders as $item) {
            JoinRequestQuestion::where('id', $item['id'])->update(['order_priority' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleVisibility($id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $question = JoinRequestQuestion::findOrFail($id);
        $question->is_active = !$question->is_active;
        $question->save();

        return response()->json(['success' => true, 'is_active' => $question->is_active]);
    }

    public function addToArchive(Request $request, $id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $question = JoinRequestQuestion::findOrFail($id);
        $question->archive_id = $request->archive_id;
        $question->save();

        return response()->json(['success' => true]);
    }

    // Archive Management
    public function storeArchive(Request $request)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        QuestionArchive::create($validated);

        return back()->with('success', 'Archive created successfully.');
    }

    public function toggleArchiveVisibility($id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $archive = QuestionArchive::findOrFail($id);
        $archive->is_active = !$archive->is_active;
        $archive->save();

        return response()->json(['success' => true, 'is_active' => $archive->is_active]);
    }

    public function destroyArchive($id)
    {
        if (!Auth::user()->canManageJoinRequests()) { abort(403); }
        $archive = QuestionArchive::findOrFail($id);
        $archive->delete();

        return back()->with('success', 'Archive deleted successfully.');
    }
}
