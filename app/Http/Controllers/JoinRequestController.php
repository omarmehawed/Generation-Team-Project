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
     * Toggle the Join Request system status (ON/OFF).
     */
    public function toggleStatus(Request $request)
    {
        $this->authorizeAdminEmails();
        
        $status = $request->input('status', 'off');
        \App\Models\Setting::set('join_request_enabled', $status);

        return response()->json(['success' => true, 'status' => $status]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\App\Models\Setting::get('join_request_enabled', 'on') !== 'on') {
            return redirect('/')->with('error', 'نعتذر، باب الانضمام مغلق حالياً. يرجى المتابعة لاحقاً.');
        }

        $questions = \App\Models\JoinRequestQuestion::where('is_active', true)
            ->orderBy('order_priority')
            ->get();

        return view('join_requests.create', compact('questions'));
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
     * Check Join Request status remotely.
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'academic_id' => 'required|string|max:20'
        ]);

        $joinRequest = JoinRequest::where('academic_id', $request->academic_id)->first();

        if (!$joinRequest) {
            return response()->json([
                'status' => 'not_found', 
                'message' => 'لم يتم العثور على طلب بهذا الرقم الأكاديمي.'
            ]);
        }

        if ($joinRequest->status === 'approved') {
            return response()->json([
                'status' => 'approved', 
                'message' => 'تمت الموافقة على طلبك. مرحباً بك!'
            ]);
        } elseif ($joinRequest->status === 'rejected') {
            return response()->json([
                'status' => 'rejected', 
                'message' => 'نعتذر، لم يتم قبول طلبك. يرجى التواصل مع الإدارة عند الحاجة.'
            ]);
        } else {
            return response()->json([
                'status' => 'pending', 
                'message' => 'طلبك لا يزال قيد المراجعة. يرجى الانتظار لحين الموافقة.'
            ]);
        }
    }

    /**
     * Store a newly created Join Request
     */
    public function store(Request $request)
    {
        // 1. Basic Rules
        $rules = [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:-17 years',
            'national_id' => 'required|string|max:20',
            'academic_id' => 'required|string|max:20',
            'group' => 'required|in:G1,G2,G3,G4',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'is_dorm' => 'required|boolean',
            'photo' => 'required|image|max:102400',
        ];

        // 2. Fetch Active Questions for dynamic validation
        $questions = \App\Models\JoinRequestQuestion::where('is_active', true)->get();
        
        foreach ($questions as $q) {
            $field = "answers." . $q->id;
            $qRules = [];
            
            if ($q->is_required) {
                $qRules[] = 'required';
            } else {
                $qRules[] = 'nullable';
            }

            if ($q->question_type === 'matrix' || $q->question_type === 'checkbox') {
                $qRules[] = 'array';
            } elseif ($q->question_type === 'scale') {
                $qRules[] = 'integer';
            } else {
                $qRules[] = 'string';
            }

            $rules[$field] = $qRules;
        }

        $validated = $request->validate($rules, [
            'date_of_birth.before_or_equal' => 'يجب أن لا يقل العمر عن 17 عاماً للتسجيل',
        ]);

        // 3. Custom Duplicate Check
        $existingRequest = JoinRequest::where('national_id', $validated['national_id'])
                                        ->orWhere('academic_id', $validated['academic_id'])
                                        ->first();

        $existingUser = \App\Models\User::where('national_id', $validated['national_id'])->first();

        if ($existingRequest || $existingUser) {
            return back()->withInput()->with('error', 'بياناتك مسجلة بالفعل في النظام.')->with('error_title', 'عفواً! لقد قمت بالتسجيل مسبقاً.');
        }

        // 4. Handle File Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('join_requests/photos', 'public');
        }

        // 5. Create Join Request
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
            'answers' => $validated['answers'] ?? [],
            'status' => 'pending',
        ]);

        return redirect('/')->with('success', 'Application Submitted! We have received your request.');
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

        // 5. Analytics Dashboard Metrics
        $totalCount = JoinRequest::count();
        $pendingCount = JoinRequest::where('status', 'pending')->count();
        $approvedCount = JoinRequest::where('status', 'approved')->count();
        $rejectedCount = JoinRequest::where('status', 'rejected')->count();

        $joinRequestEnabled = \App\Models\Setting::get('join_request_enabled', 'on');
        $questions = \App\Models\JoinRequestQuestion::all();

        return view('join_requests.index', compact('requests', 'totalCount', 'pendingCount', 'approvedCount', 'rejectedCount', 'joinRequestEnabled', 'questions'));
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
