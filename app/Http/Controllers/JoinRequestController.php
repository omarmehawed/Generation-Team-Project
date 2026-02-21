<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class JoinRequestController extends Controller
{
    /**
     * Display the Landing Page.
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('join_requests.create');
    }

    /**
     * Check Admin Access (Email Based)
     */
    private function authorizeAdminEmails()
    {
        $allowedEmails = ['2420823@batechu.com', '2420324@batechu.com'];

        if (!Auth::check() || !in_array(Auth::user()->email, $allowedEmails)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function checkDuplicate(Request $request)
    {
        $nid = $request->query('nid');
        if (!$nid) {
            return response()->json(['exists' => false]);
        }
        
        $existsInRequests = JoinRequest::where('national_id', $nid)->exists();
        $existsInUsers = User::where('national_id', $nid)->exists();
        
        return response()->json([
            'exists' => $existsInRequests || $existsInUsers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Section 1: Basic Info
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:-17 years',
            'national_id' => 'required|string|max:20',
            'academic_id' => 'required|string|max:20',
            'group' => 'required|in:G1,G2,G3,G4',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'is_dorm' => 'required|boolean',
            'photo' => 'required|image|max:102400', // 100MB Max

            // Section 2: Questions (Answers array)
            // Section 2: Questions (Answers array)
            'answers' => 'required|array',
            
            // 1. Experience Field
            'answers.experience_field' => 'required|string',
            
            // 2. Large Team
            'answers.large_team_experience' => 'required|in:Yes,No',
            
            // 3. Start Date
            'answers.start_date' => 'required|date',
            
            // 4. Weekly Hours
            'answers.weekly_hours' => 'required|string',
            
            // 5. Best Project
            'answers.best_project' => 'required|string',
            
            // 6. Confidence Scale
            'answers.confidence_scale' => 'required|integer|min:1|max:5',
            
            // 7. Team Skills (Matrix)
            'answers.team_skills' => 'required|array',
            'answers.team_skills.*' => 'required|string', // Check all skills are answered
            
            // 8. Programming Language
            'answers.programming_language' => 'required|string',
            
            // 9. Prototyping Skills (Matrix)
            'answers.prototyping_skills' => 'required|array',
            'answers.prototyping_skills.*' => 'required|string',
            
            // 10. Funding
            'answers.funding_experience' => 'required|in:Yes,No',
            
            // 11. Tools Usage (Matrix)
            'answers.tools_usage' => 'required|array',
            'answers.tools_usage.*' => 'required|string',
            
            // 12. Voice Assistants
            'answers.voice_assistants_realtime' => 'required|in:Yes,No',
            
            // 13. Importance
            'answers.project_importance' => 'required|integer|min:1|max:5',
            
            // 14. Stress
            'answers.stress_handling' => 'required|string',
        ], [
            'full_name.required' => 'Full Name is required',
            'national_id.required' => 'National ID is required',
            'academic_id.required' => 'Academic ID is required',
            'date_of_birth.required' => 'Date of Birth is required',
            'date_of_birth.before_or_equal' => 'يجب أن لا يقل العمر عن 17 عاماً للتسجيل',
            'group.required' => 'Please select your Group (G1-G4)',
            'phone_number.required' => 'Phone Number is required',
            'whatsapp_number.required' => 'WhatsApp Number is required',
            'address.required' => 'Address is required',
            'photo.required' => 'A formal photo is required',
            'photo.image' => 'The file must be an image',
            'photo.max' => 'The photo size must not exceed 100MB',
            'answers.experience_field.required' => 'Please select your Experience Field',
            'answers.large_team_experience.required' => 'Please answer the Team Experience question',
            'answers.start_date.required' => 'Please specify when you can start',
            'answers.weekly_hours.required' => 'Please select your Weekly Hours availability',
            'answers.best_project.required' => 'Please describe your Best Project',
            'answers.confidence_scale.required' => 'Please rate your Confidence',
            'answers.team_skills.required' => 'Please complete the Team Skills matrix',
            'answers.programming_language.required' => 'Please select your Primary Programming Language',
            'answers.prototyping_skills.required' => 'Please complete the Prototyping Skills matrix',
            'answers.funding_experience.required' => 'Please answer the Funding Experience question',
            'answers.tools_usage.required' => 'Please complete the Tools Usage matrix',
            'answers.voice_assistants_realtime.required' => 'Please answer the Voice Assistants question',
            'answers.project_importance.required' => 'Please rate the Project Importance',
            'answers.stress_handling.required' => 'Please explain how you handle stress',
        ]);

        // Custom Duplicate Check
        $existingRequest = JoinRequest::where('national_id', $validated['national_id'])
                                        ->orWhere('academic_id', $validated['academic_id'])
                                        ->first();

        $existingUser = User::where('national_id', $validated['national_id'])->first();

        if ($existingRequest) {
            $msg = 'تم تسجيل طلبك بالفعل بالرقم الأكاديمي: ' . $existingRequest->academic_id . '. يرجى انتظار المراجعة.';
            return back()->withInput()->with('error', $msg)->with('error_title', 'عفواً! لقد قمت بالتسجيل مسبقاً.');
        }

        if ($existingUser) {
            $msg = 'لديك حساب فعال بالفعل في النظام.';
            return back()->withInput()->with('error', $msg)->with('error_title', 'عفواً! لقد قمت بالتسجيل مسبقاً.');
        }

        // Handle File Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = Cloudinary::uploadApi()->upload($request->file('photo')->getRealPath(), ['folder' => 'join_requests_photos'])['secure_url'];
        }

        // Create Join Request
        JoinRequest::create([
            'full_name' => $validated['full_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'national_id' => $validated['national_id'],
            'academic_id' => $validated['academic_id'],
            'group' => $validated['group'],
            'phone_number' => $validated['phone_number'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'address' => $validated['address'],
            'is_dorm' => $validated['is_dorm'],
            'photo_path' => $photoPath,
            'answers' => $validated['answers'],
            'status' => 'pending',
        ]);

        return redirect()->route('join.success');
    }

    /**
     * Admin: List all requests (with Search, Sort, Pagination).
     */
    public function adminIndex(Request $request)
    {
        $this->authorizeAdminEmails();

        $query = JoinRequest::with(['user', 'approver']);

        // 1. Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('academic_id', 'like', "%{$search}%"); // Added Academic ID search
            });
        }

        // 2. Filters
        if ($request->has('status') && $request->filled('status')) {
            $query->where('status', $request->input('status')); // pending, approved, rejected
        }

        if ($request->has('date') && $request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 3. Sorting
        $sortColumn = $request->input('sort_by', 'created_at'); // Default sort
        $sortDirection = $request->input('sort_order', 'desc');

        // Allow sorting only on specific columns
        $allowedSorts = ['full_name', 'group', 'status', 'created_at'];
        if (in_array($sortColumn, $allowedSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->latest();
        }

        // 4. Pagination
        $requests = $query->paginate(10)->withQueryString();

        return view('join_requests.index', compact('requests'));
    }

    /**
     * Admin: Show Approve Form.
     */
    public function approve($id)
    {
        $this->authorizeAdminEmails();
        $joinRequest = JoinRequest::findOrFail($id);
        return view('join_requests.approve', compact('joinRequest'));
    }

    /**
     * Admin: Store User and Link.
     */
    public function storeUser(Request $request, $id)
    {
        $this->authorizeAdminEmails();
        $joinRequest = JoinRequest::findOrFail($id);
        
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create User
        $user = User::create([
            'name' => $joinRequest->full_name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'national_id' => $joinRequest->national_id,
            'phone_number' => $joinRequest->phone_number,
            'whatsapp_number' => $joinRequest->whatsapp_number, // [NEW] Sync WhatsApp
            'date_of_birth' => $joinRequest->date_of_birth, // [NEW] Sync DOB
            'address' => $joinRequest->address, // [NEW] Map Address to Address
            'is_dorm' => $joinRequest->is_dorm,
            'profile_photo_path' => $joinRequest->photo_path,
            'role' => 'student', // Default role for new members
            'academic_year' => 2, // [NEW] Default to 2nd Year
            'department' => 'general', // [NEW] Default to General Department
            'is_team_leader' => false,
            // Map academic_id to university_email if it's not set, assuming standard format or just store it if we added a column
            // 'university_email' => $joinRequest->academic_id . '@batechu.com', // Optional: Auto-generate?
            // Add other fields as needed based on User model
        ]);

        // Update Join Request
        $joinRequest->update([
            'status' => 'approved',
            'user_id' => $user->id,
            'approved_by' => Auth::id(),
        ]);

        // Move photo if needed? 
        // For now we keep it in the same path as it's accessible.

        return redirect()->route('join.admin')->with('success', 'User created successfully!');
    }

    /**
     * Admin: Reject Request.
     */
    public function reject($id)
    {
        $this->authorizeAdminEmails();
        $joinRequest = JoinRequest::findOrFail($id);
        
        $joinRequest->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('join.admin')->with('error', 'Request rejected.');
    }

    /**
     * Admin: Export Requests to CSV.
     */
    public function export(Request $request)
    {
        $this->authorizeAdminEmails();

        $status = $request->input('status', 'all'); // all, pending, approved, rejected
        $columns = $request->input('columns', ['full_name', 'status']); // Default columns

        // Map database columns to human-readable headers
        $columnMapping = [
            'full_name' => 'Full Name',
            'national_id' => 'National ID',
            'academic_id' => 'Academic ID',
            'phone_number' => 'Phone Number',
            'whatsapp_number' => 'WhatsApp Number',
            'address' => 'Home Address',
            'group' => 'Group',
            'date_of_birth' => 'Date of Birth',
            'is_dorm' => 'Dorm Status',
            'status' => 'Status',
            'created_at' => 'Request Date'
        ];

        // Filter requested columns against allowed mapping
        $selectedKeys = array_intersect($columns, array_keys($columnMapping));
        
        if (empty($selectedKeys)) {
            $selectedKeys = ['full_name', 'status']; // Default fallback
        }

        // Build Query
        $query = JoinRequest::query();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->select($selectedKeys)->get();

        // Generate CSV
        $filename = "join_requests_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://memory', 'w');

        // Add BOM for Excel compatibility with Arabic
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write Headers (Human Readable)
        $headers = [];
        foreach ($selectedKeys as $key) {
            $headers[] = $columnMapping[$key];
        }
        fputcsv($handle, $headers);

        // Write Rows with Formatting
        foreach ($requests as $req) {
            $row = [];
            foreach ($selectedKeys as $key) {
                $value = $req->$key;
                
                // Format Specific Columns
                if ($key === 'is_dorm') {
                    $value = $value ? 'Yes' : 'No';
                } elseif ($key === 'status') {
                    $value = ucfirst($value);
                } elseif ($key === 'created_at') {
                    $value = $value ? $value->format('Y-m-d H:i') : '';
                }

                $row[] = $value;
            }
            fputcsv($handle, $row);
        }

        fseek($handle, 0);
        $csvData = stream_get_contents($handle);
        fclose($handle);

        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
