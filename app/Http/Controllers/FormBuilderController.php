<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\Team;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormBuilderController extends Controller
{
    public function index()
    {
        $forms = Form::withCount('responses')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('forms.manage.index', compact('forms'));
    }

    public function create()
    {
        $teams = Team::all();
        return view('forms.manage.builder', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'deadline' => 'nullable|date',
            'allow_edit_response' => 'boolean',
            'assigned_teams' => 'nullable|array',
            'assigned_roles' => 'nullable|array',
            'questions' => 'required|array',
            'questions.*.title' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.is_required' => 'boolean',
            'questions.*.options' => 'nullable|array',
        ]);

        $form = Form::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by_id' => Auth::id(),
            'is_active' => $validated['is_active'] ?? true,
            'deadline' => $validated['deadline'] ?? null,
            'allow_edit_response' => $validated['allow_edit_response'] ?? false,
            'assigned_teams' => $validated['assigned_teams'] ?? null,
            'assigned_roles' => $validated['assigned_roles'] ?? null,
        ]);

        foreach ($validated['questions'] as $index => $q) {
            FormQuestion::create([
                'form_id' => $form->id,
                'title' => $q['title'],
                'type' => $q['type'],
                'is_required' => $q['is_required'] ?? false,
                'options' => $q['options'] ?? null,
                'order' => $index,
            ]);
        }

        ActivityLog::create([
            'causer_id' => Auth::id(),
            'subject_id' => $form->id,
            'subject_type' => Form::class,
            'action' => 'Created',
            'description' => 'Created form: ' . $form->title,
            'changes' => ['attributes' => $form->toArray()]
        ]);

        return response()->json(['success' => true, 'redirect' => route('forms.manage.index')]);
    }

    public function edit(Form $form)
    {
        $form->load('questions');
        $teams = Team::all();
        return view('forms.manage.builder', compact('form', 'teams'));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'deadline' => 'nullable|date',
            'allow_edit_response' => 'boolean',
            'assigned_teams' => 'nullable|array',
            'assigned_roles' => 'nullable|array',
            'questions' => 'required|array',
        ]);

        $form->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'deadline' => $validated['deadline'] ?? null,
            'allow_edit_response' => $validated['allow_edit_response'] ?? false,
            'assigned_teams' => $validated['assigned_teams'] ?? null,
            'assigned_roles' => $validated['assigned_roles'] ?? null,
        ]);

        $keptIds = [];
        
        foreach ($validated['questions'] as $index => $q) {
            if (isset($q['id']) && $q['id']) {
                $question = FormQuestion::find($q['id']);
                if ($question && $question->form_id == $form->id) {
                    $question->update([
                        'title' => $q['title'],
                        'type' => $q['type'],
                        'is_required' => $q['is_required'] ?? false,
                        'options' => $q['options'] ?? null,
                        'order' => $index,
                    ]);
                    $keptIds[] = $question->id;
                }
            } else {
                $newQ = FormQuestion::create([
                    'form_id' => $form->id,
                    'title' => $q['title'],
                    'type' => $q['type'],
                    'is_required' => $q['is_required'] ?? false,
                    'options' => $q['options'] ?? null,
                    'order' => $index,
                ]);
                $keptIds[] = $newQ->id;
            }
        }

        FormQuestion::where('form_id', $form->id)->whereNotIn('id', $keptIds)->delete();

        ActivityLog::create([
            'causer_id' => Auth::id(),
            'subject_id' => $form->id,
            'subject_type' => Form::class,
            'action' => 'Updated',
            'description' => 'Updated form: ' . $form->title,
            'changes' => ['attributes' => $form->toArray()]
        ]);

        return response()->json(['success' => true, 'redirect' => route('forms.manage.index')]);
    }

    public function toggleStatus(Form $form)
    {
        $form->update(['is_active' => !$form->is_active]);
        return back()->with('success', 'Form status updated.');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        ActivityLog::create([
            'causer_id' => Auth::id(),
            'subject_id' => $form->id,
            'subject_type' => Form::class,
            'action' => 'Deleted',
            'description' => 'Deleted form: ' . $form->title,
        ]);
        return redirect()->route('forms.manage.index')->with('success', 'Form deleted.');
    }
}
