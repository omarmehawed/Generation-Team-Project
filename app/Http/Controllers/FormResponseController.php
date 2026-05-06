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
        
        // Complex filtering based on assignments could be done here.
        // For now, let's fetch active forms
        $formsQuery = Form::where('is_active', true);
        
        $forms = $formsQuery->with(['responses' => function($q) use ($user) {
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

                // Create or update the answer. If the value is missing in an edit, it will overwrite with null/[]
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

        return redirect()->route('forms.member.index')->with('success', 'Form submitted successfully.');
    }
}
