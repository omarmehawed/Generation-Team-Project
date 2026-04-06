<?php

namespace App\Http\Controllers;

use App\Models\JoinRequestQuestion;
use Illuminate\Http\Request;

class JoinRequestQuestionController extends Controller
{
    public function index()
    {
        $questions = JoinRequestQuestion::orderBy('order_priority')->get();
        return view('join_requests.questions.index', compact('questions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'section' => 'nullable|string',
            'conditional_logic' => 'nullable|array',
        ]);

        $maxOrder = JoinRequestQuestion::max('order_priority') ?? 0;
        $validated['order_priority'] = $maxOrder + 1;

        JoinRequestQuestion::create($validated);

        return back()->with('success', 'Question added successfully.');
    }

    public function edit($id)
    {
        $question = JoinRequestQuestion::findOrFail($id);
        return response()->json($question);
    }

    public function update(Request $request, $id)
    {
        $question = JoinRequestQuestion::findOrFail($id);
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'section' => 'string',
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
        $question = JoinRequestQuestion::findOrFail($id);
        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders'); // Array of {id, order}
        
        foreach ($orders as $item) {
            JoinRequestQuestion::where('id', $item['id'])->update(['order_priority' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
