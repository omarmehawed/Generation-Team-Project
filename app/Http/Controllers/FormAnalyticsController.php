<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class FormAnalyticsController extends Controller
{
    public function show(Form $form)
    {
        $form->load(['questions.answers', 'responses.user']);

        // Fetch activity logs for this form
        $logs = ActivityLog::where('subject_type', Form::class)
            ->where('subject_id', $form->id)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate basic stats
        $totalResponses = $form->responses->count();

        // Generate data for charts
        $chartData = [];
        foreach ($form->questions as $q) {
            if (in_array($q->type, ['multiple_choice', 'dropdown', 'checkboxes', 'rating'])) {
                $counts = [];
                foreach ($q->answers as $ans) {
                    if ($q->type === 'checkboxes') {
                        $vals = $ans->answer_json ?? [];
                        foreach ($vals as $v) {
                            $counts[$v] = ($counts[$v] ?? 0) + 1;
                        }
                    } else {
                        $v = $ans->answer_text;
                        if ($v) {
                            $counts[$v] = ($counts[$v] ?? 0) + 1;
                        }
                    }
                }
                $chartData[$q->id] = [
                    'labels' => array_keys($counts),
                    'data' => array_values($counts)
                ];
            }
        }

        return view('forms.manage.analytics', compact('form', 'logs', 'totalResponses', 'chartData'));
    }

    public function viewResponse(\App\Models\FormResponse $response)
    {
        $response->load(['answers.question', 'user', 'form']);
        return view('forms.manage.response_show', compact('response'));
    }
}
