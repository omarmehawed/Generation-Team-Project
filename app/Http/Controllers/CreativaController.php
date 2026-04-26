<?php

namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use App\Models\Submission;
 
 class CreativaController extends Controller
 {
     public function index()
     {
         return view('creativa.submit');
     }
 
     public function draw()
     {
         return view('creativa.draw');
     }
 
     public function getPool()
     {
         return response()->json(Submission::where('used', 0)->get());
     }
 
     public function getAll()
     {
         return response()->json(Submission::all());
     }
 
     public function submit(Request $request)
     {
         $validated = $request->validate([
             'name' => 'required|string|max:255',
             'department' => 'required|string|max:255',
             'question' => 'required|string',
         ]);
 
         $submission = Submission::create($validated);
 
         return response()->json($submission, 201);
     }
 
     public function markUsed($id)
     {
         $submission = Submission::findOrFail($id);
         $submission->update(['used' => 1]);
 
         return response()->json(['message' => 'Marked as used']);
     }
 }
