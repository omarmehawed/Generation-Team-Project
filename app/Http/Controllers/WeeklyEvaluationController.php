<?php

namespace App\Http\Controllers;

use App\Models\WeeklyEvaluation;
use App\Models\EvaluationItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Illuminate\Support\Str;

class WeeklyEvaluationController extends Controller
{
    public function getWeekData(Request $request, $studentId, $week)
    {
        $student = User::findOrFail($studentId);
        
        // Authorization check (Leader only)
        // if (Auth::user()->cannot('evaluate', $student)) abort(403);

        $evaluation = WeeklyEvaluation::with('items')->firstOrNew([
            'student_id' => $student->id,
            'week_number' => $week
        ]);

        // Pre-fill default items if new (Optional, but good for UI)
        if (!$evaluation->exists) {
            // We can return empty structure or default items
        }

        return response()->json([
            'html' => view('profile.partials.weekly-evaluation-form', compact('student', 'evaluation', 'week'))->render()
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Weekly Evaluation Store Request:', $request->all());

        // Filter out empty items before validation
        $categories = ['tasks', 'quizzes', 'meetings', 'workshops', 'activities'];
        foreach ($categories as $category) {
            if ($request->has($category) && is_array($request->$category)) {
                $filtered = array_filter($request->$category, function ($item) {
                    return !empty($item['title']);
                });
                $request->merge([$category => array_values($filtered)]);
            }
        }

        // ... (validation remains similar, but need to handle student_id from request if not in route)
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'week_number' => 'required|integer',
            'commitment_level' => 'required|integer|min:1|max:5',
            'satisfaction_level' => 'required|integer|min:1|max:5',
            'general_notes' => 'nullable|string',
            
            // New Array Validations
            'tasks' => 'nullable|array',
            'tasks.*.type' => 'required|string',
            'tasks.*.title' => 'required|string',
            'tasks.*.rating' => 'nullable|string',
            'tasks.*.note' => 'nullable|string',

            'quizzes' => 'nullable|array',
            'quizzes.*.type' => 'required|string',
            'quizzes.*.title' => 'required|string',
            'quizzes.*.mark' => 'required|numeric',

            'meetings' => 'nullable|array',
            'meetings.*.type' => 'required|string',
            'meetings.*.title' => 'required|string',
            'meetings.*.rating' => 'required|string',

            'workshops' => 'nullable|array',
            'workshops.*.type' => 'required|string',
            'workshops.*.title' => 'required|string',
            'workshops.*.rating' => 'required|string',

            'activities' => 'nullable|array',
            'activities.*.type' => 'required|string',
            'activities.*.title' => 'required|string',
            'activities.*.rating' => 'required|string',
        ]);
        
        $student = User::findOrFail($data['student_id']);

        $evaluation = WeeklyEvaluation::updateOrCreate(
            [
                'student_id' => $student->id,
                'week_number' => $data['week_number']
            ],
            [
                'commitment_level' => $data['commitment_level'],
                'satisfaction_level' => $data['satisfaction_level'],
                'general_notes' => $data['general_notes'] ?? '',
                'created_by' => Auth::id()
            ]
        );

        // Sync Items
        $evaluation->items()->delete();
        
        $allItems = [];
        if (!empty($data['tasks'])) $allItems = array_merge($allItems, $data['tasks']);
        if (!empty($data['quizzes'])) $allItems = array_merge($allItems, $data['quizzes']);
        if (!empty($data['meetings'])) $allItems = array_merge($allItems, $data['meetings']);
        if (!empty($data['workshops'])) $allItems = array_merge($allItems, $data['workshops']);
        if (!empty($data['activities'])) $allItems = array_merge($allItems, $data['activities']);

        foreach ($allItems as $index => $item) {
            $evaluation->items()->create([
                'type' => $item['type'],
                'title' => $item['title'],
                'rating' => $item['rating'] ?? null,
                'mark' => $item['mark'] ?? null,
                'note' => $item['note'] ?? null,
                'order' => $index
            ]);
        }
        
        // Generate PDF
        $pdfPath = $this->generatePdfInternal($evaluation);
        $evaluation->update(['pdf_path' => $pdfPath]);

        return response()->json(['success' => true, 'pdf_url' => asset('storage/' . $pdfPath)]);
    }

    public function downloadPdf($id)
    {
        $evaluation = WeeklyEvaluation::findOrFail($id);
        // Auth check...

        $path = storage_path('app/public/' . $evaluation->pdf_path);
        if (!file_exists($path)) abort(404);

        return response()->download($path);
    }

    private function generatePdfInternal(WeeklyEvaluation $evaluation)
    {
        $pdf = Pdf::loadView('pdf.weekly_evaluation', compact('evaluation'));
        $filename = 'evaluations/' . $evaluation->student_id . '/week_' . $evaluation->week_number . '.pdf';
        
        $path = storage_path('app/public/' . dirname($filename));
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        
        $pdf->save(storage_path('app/public/' . $filename));
        return $filename;
    }
}
