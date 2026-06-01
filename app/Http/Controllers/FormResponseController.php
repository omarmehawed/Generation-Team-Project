<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormResponse;
use App\Models\FormAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormResponseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Filter active forms visible to this user's gender
        $forms = Form::where('is_active', true)
            ->visibleToUser($user)
            ->with(['responses' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->get();

        return view('forms.member.index', compact('forms'));
    }

    public function show(Form $form)
    {
        if (!$form->is_active) {
            abort(403, 'This form is no longer active.');
        }

        $existingResponse = FormResponse::where('form_id', $form->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingResponse && !$form->allow_edit_response) {
            return redirect()->route('forms.member.index')->with('error', 'You have already submitted this form.');
        }

        $form->load('questions');
        return view('forms.member.show', compact('form', 'existingResponse'));
    }

    public function store(Request $request, Form $form)
    {
        if (!$form->is_active) {
            abort(403, 'This form is no longer active.');
        }

        $existingResponse = FormResponse::where('form_id', $form->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingResponse && !$form->allow_edit_response) {
            return back()->with('error', 'You have already submitted this form.');
        }

        // Validate
        $rules = [];
        foreach ($form->questions as $q) {
            if ($q->is_required) {
                if ($q->type === 'file_upload') {
                    $hasExistingFile = false;
                    if ($existingResponse) {
                        $savedFile = $existingResponse->answers()->where('form_question_id', $q->id)->first()?->answer_file;
                        if ($savedFile) {
                            $hasExistingFile = true;
                        }
                    }
                    if (!$hasExistingFile) {
                        $rules['answers.'.$q->id] = 'required|file';
                    }
                } else if ($q->type === 'checkboxes') {
                    $rules['answers.'.$q->id] = 'required|array';
                } else {
                    $rules['answers.'.$q->id] = 'required';
                }
            }
        }
        $request->validate($rules);

        $response = $existingResponse ?: FormResponse::create([
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'status' => 'submitted'
        ]);

        // Save answers
        $this->saveAnswers($request, $form, $response, $existingResponse);

        return redirect()->route('forms.member.index')->with('success', 'Form submitted successfully.');
    }

    /**
     * Show a mandatory form (dedicated route for forced completion).
     */
    public function showMandatory(Form $form)
    {
        $user = Auth::user();

        // Verify this form is actually mandatory for this user
        if (!$form->is_required || !$form->is_active) {
            return redirect()->route('forms.member.index');
        }

        // Check gender targeting
        if ($form->target_gender !== 'all' && $form->target_gender !== $user->gender) {
            return redirect()->route('projects.index');
        }

        // Enforce sequence: user must complete the oldest mandatory form first
        $firstMandatoryForm = Form::mandatoryForUser($user)->first();
        if ($firstMandatoryForm && $firstMandatoryForm->id !== $form->id) {
            return redirect()->route('forms.mandatory.show', $firstMandatoryForm->id);
        }

        // Check if already submitted
        $existingResponse = FormResponse::where('form_id', $form->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            // Already submitted — check for next mandatory form or go to dashboard
            $nextForm = Form::mandatoryForUser($user)->first();
            if ($nextForm) {
                return redirect()->route('forms.mandatory.show', $nextForm->id);
            }
            return redirect()->route('projects.index');
        }

        $form->load('questions');
        return view('forms.member.mandatory', compact('form'));
    }

    /**
     * Process a mandatory form submission.
     */
    public function storeMandatory(Request $request, Form $form)
    {
        $user = Auth::user();

        if (!$form->is_required || !$form->is_active) {
            abort(403, 'This form is not mandatory.');
        }

        // Check gender targeting
        if ($form->target_gender !== 'all' && $form->target_gender !== $user->gender) {
            abort(403, 'This form does not apply to you.');
        }

        // Enforce sequence: user must complete the oldest mandatory form first
        $firstMandatoryForm = Form::mandatoryForUser($user)->first();
        if ($firstMandatoryForm && $firstMandatoryForm->id !== $form->id) {
            return redirect()->route('forms.mandatory.show', $firstMandatoryForm->id);
        }

        $existingResponse = FormResponse::where('form_id', $form->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            return redirect()->route('projects.index');
        }

        // Validate
        $rules = [];
        foreach ($form->questions as $q) {
            if ($q->is_required) {
                if ($q->type === 'file_upload') {
                    $rules['answers.'.$q->id] = 'required|file';
                } else if ($q->type === 'checkboxes') {
                    $rules['answers.'.$q->id] = 'required|array';
                } else {
                    $rules['answers.'.$q->id] = 'required';
                }
            }
        }
        $request->validate($rules);

        $response = FormResponse::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'status' => 'submitted'
        ]);

        // Save answers
        $this->saveAnswers($request, $form, $response, null);

        // Check for next mandatory form
        $nextForm = Form::mandatoryForUser($user)->first();
        if ($nextForm) {
            return redirect()->route('forms.mandatory.show', $nextForm->id)
                ->with('success', 'Form submitted! Please complete the next required form.');
        }

        return redirect()->route('projects.index')
            ->with('success', 'All mandatory forms completed! Welcome to the platform.');
    }

    /**
     * Shared logic for saving form answers.
     */
    private function saveAnswers(Request $request, Form $form, FormResponse $response, ?FormResponse $existingResponse)
    {
        foreach ($form->questions as $q) {
            if ($q->type === 'file_upload') {
                if ($request->hasFile('answers.'.$q->id)) {
                    $ansFile = $request->file('answers.'.$q->id)->store('forms/uploads', 'public');
                    FormAnswer::updateOrCreate(
                        ['form_response_id' => $response->id, 'form_question_id' => $q->id],
                        ['answer_file' => $ansFile]
                    );
                }
            } else {
                $ansVal = $request->input('answers.'.$q->id);
                $ansText = null;
                $ansJson = null;

                if ($q->type === 'checkboxes') {
                    $ansJson = is_array($ansVal) ? $ansVal : [];
                } else {
                    $ansText = is_array($ansVal) ? json_encode($ansVal) : $ansVal;
                }

                // Create or update the answer
                if ($existingResponse || $ansVal !== null || $q->type === 'checkboxes') {
                    FormAnswer::updateOrCreate(
                        ['form_response_id' => $response->id, 'form_question_id' => $q->id],
                        [
                            'answer_text' => $ansText,
                            'answer_json' => $ansJson
                        ]
                    );
                }
            }
        }
    }
}
