<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Setting; // ✅ استدعاء موديل الإعدادات
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // 1. تحميل اليوزرز مع العلاقات
        // 1. استدعاء creator بدل editor
        // نحمل العلاقات (creator و deleter عشان سلة المحذوفات)
        $query = User::with(['courses', 'creator', 'deleter']);
        // 🗑️ هل التشيك بوكس بتاع "سلة المحذوفات" مضغوط؟
        if ($request->has('trash') && $request->trash == '1') {
            $query->onlyTrashed(); // هات المحذوفين بس

            // 🔥 شرط العزل في سلة المحذوفات:
            // لو مش أدمن -> يشوف بس الناس اللي "هو دليتهم"
            if (Auth::user()->role !== 'admin') {
                $query->where('deleted_by_id', Auth::id());
            }
        } else {
            // الوضع العادي (مش محذوفين)
            // لو مش أدمن -> يشوف اللي "هو كريتهم"
            if (Auth::user()->role !== 'admin') {
                $query->where('created_by_id', Auth::id());
            }
        }
        // 2. فلترة الدكاترة (يشوفوا اللي هما عملوه بس)
        if (Auth::user()->role !== 'admin') {
            $query->where('created_by_id', Auth::id());
        }

        // السيرش والفلتر
        if ($request->has('search') && $request->search != '') {
            $value = $request->search;
            $query->where(function ($q) use ($value) {
                $q->where('name', 'LIKE', "%{$value}%")
                    ->orWhere('email', 'LIKE', "%{$value}%");
            });
        }

        // 4. 🔥 فلتر التاريخ (من - إلى) [تعديل جديد]
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        // 2. الرتبة
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 3. القسم
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        if ($request->has('academic_year') && $request->academic_year != '') {
            $query->where('academic_year', $request->academic_year);
        }

        // التعديل: قسمهم صفحات، كل صفحة فيها 20 طالب
        $users = $query->latest()->paginate(20);
        $courses = Course::all(); // عشان المودال للدكاترة

        return view('admin.users', compact('users', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_by_id' => Auth::id(), // تسجيل الأدمن
        ];

        // لو طالب
        if ($request->role === 'student') {
            $request->validate([
                'academic_year' => 'required', 
                'department' => 'required',
                'national_id' => 'required|digits:14|unique:users,national_id'
            ]);
            $data['academic_year'] = $request->academic_year;
            $data['department'] = $request->department;
            $data['national_id'] = $request->national_id;
            $data['permissions'] = null;
        }
        // لو دكتور/معيد/أدمن
        else {
            $data['academic_year'] = 0;
            $data['department'] = 'general';
            $data['permissions'] = $request->permissions ?? [];
        }

        // 1. إنشاء اليوزر
        $user = User::create($data);

        // 2. توزيع المواد (Courses Allocation)
        if ($request->role === 'student') {
            // 🔥🔥 السحر هنا: لو طالب، هاتله مواد الترم الحالي أوتوماتيك
            $this->assignStudentCourses($user);
        } elseif (in_array($request->role, ['doctor', 'ta', 'admin']) && $request->has('courses')) {
            // لو دكتور، خد المواد اللي الأدمن اختارها مانيوال
            $user->courses()->sync($request->courses);
        }

        return back()->with('success', 'User added successfully (Courses assigned based on current term).');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1️⃣ حماية الرتب: ممنوع حد غير الأدمن يخلي حد أدمن
        if (Auth::user()->role !== 'admin' && $request->role === 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
        ]);

        // 2️⃣ تجهيز البيانات (شلنا last_editor_id خلاص)
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            // ❌ 'last_editor_id' => Auth::id(),  <-- مسحناها عشان العمود اتمسح من الداتا بيز
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3️⃣ لو تحول لطالب أو هو طالب واتعدلت بياناته
        if ($request->role === 'student') {
            $request->validate([
                'academic_year' => 'required', 
                'department' => 'required',
                'national_id' => 'required|digits:14|unique:users,national_id,' . $id
            ]);
            
            $data['academic_year'] = $request->academic_year;
            $data['department'] = $request->department;
            $data['national_id'] = $request->national_id;
            $data['permissions'] = null;

            // تحديث البيانات الأول
            $user->update($data);

            // 🔥🔥 إعادة توزيع المواد أوتوماتيك (مهمة جداً هنا)
            // عشان لو غير سنته أو قسمه، المواد تتظبط فوراً
            $this->assignStudentCourses($user);
        } else {
            // 4️⃣ لو ستاف (Staff)
            $data['academic_year'] = 0;
            $data['national_id'] = null; // Clear National ID for non-students

            // ناخد الصلاحيات اللي جاية من الفورم
            $submittedPermissions = $request->permissions ?? [];

            // 🔥🔥 اللوجيك الذكي لحماية صلاحية الـ Backup 🔥🔥
            // هل أنا (الأدمن الحالي) معايا المفتاح؟
            if (!Auth::user()->hasPermission('backup_db')) {

                // ⛔ أ. ممنوع "أمنح" الصلاحية لنفسي أو لغيري (لو حاولت أبعتها كود)
                $submittedPermissions = array_diff($submittedPermissions, ['backup_db']);

                // ⛔ ب. ممنوع "أسحب" الصلاحية من حد معاه (زي السوبر أدمن)
                if ($user->hasPermission('backup_db')) {
                    $submittedPermissions[] = 'backup_db';
                }
            }

            // اعتماد الصلاحيات النهائية (array_values عشان يرتب المصفوفة صح JSON)
            $data['permissions'] = array_values($submittedPermissions);

            $user->update($data);

            // تحديث مواد الستاف (مانيوال زي ما هي)
            $user->courses()->sync($request->courses ?? []);
        }

        return back()->with('success', 'User updated successfully!');
    }

    // 🛠️ دالة مساعدة خاصة (Private Helper)
    // وظيفتها: تشوف إحنا في أي ترم، وتجيب المواد المناسبة للطالب ده
    private function assignStudentCourses($user)
    {
        // 1. هات الترم الحالي من الإعدادات (Default: 1)
        $currentTerm = Setting::where('key', 'current_term')->value('value') ?? 1;

        // 2. هات المواد المناسبة (سنة الطالب + الترم الحالي + قسمه أو عام)
        $courses = Course::where('year_level', $user->academic_year)
            ->where('term', $currentTerm)
            ->where(function ($q) use ($user) {
                $q->where('department', 'general')
                    ->orWhere('department', $user->department);
            })
            ->get();

        // 3. سجل المواد دي للطالب
        $user->courses()->sync($courses->pluck('id'));
    }

    // 2️⃣ دالة الحذف (Soft Delete) المعدلة
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // 🔥 نسجل مين اللي مسح قبل ما نمسح
            $user->deleted_by_id = Auth::id();
            $user->saveQuietly();

            $user->delete();

            // مسح التيمات المتعلقة (اختياري حسب البيزنس بتاعك)
            // \App\Models\Team::where('leader_id', $id)->delete();

            $user->delete(); // Soft Delete

            return back()->with('success', 'User moved to trash 🗑️');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting user.');
        }
    }

    // 3️⃣ دالة الاسترجاع (Restore) - جديدة
    public function restore($id)
    {
        // بنبحث في المحذوفين
        $user = User::onlyTrashed()->findOrFail($id);

        // نمسح علامة "مين اللي مسح" لأننا رجعناه خلاص
        $user->deleted_by_id = null;
        $user->save();

        $user->restore();

        return back()->with('success', 'User restored successfully! ♻️');
    }

    // 4️⃣ دالة الحذف النهائي (Force Delete) - جديدة
    public function forceDelete($id)
    {
        // بنبحث في المحذوفين
        $user = User::onlyTrashed()->findOrFail($id);

        // هنا بيتمسح من الداتا بيز للأبد
        $user->forceDelete();

        return back()->with('success', 'User permanently deleted 🚫');
    }

    public function import(Request $request)
    {
        set_time_limit(300);
        $request->validate(['file' => 'required|mimes:csv,txt']);
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");
        fgetcsv($handle);

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            try {
                $user = User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'role' => $row[2] ?? 'student',
                    'academic_year' => $row[3] ?? 1,
                    'department' => $row[4] ?? 'general',
                    'password' => Hash::make($row[5] ?? '12345678'),
                    'last_editor_id' => Auth::id(),
                ]);

                // ✅ حتى في الـ Import بنوزع المواد أوتوماتيك للطلبة
                if ($user->role === 'student') {
                    $this->assignStudentCourses($user);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        fclose($handle);
        return back()->with('success', 'Import successful (Courses auto-assigned).');
    }
    // 📥 تحميل قالب الإكسيل (Sample CSV)
    public function downloadSampleCsv()
    {
        $filename = "users_import_template.csv";

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // 🔥 إضافة BOM عشان العربي يظهر صح في الإكسيل
            fputs($handle, "\xEF\xBB\xBF");

            // 1. عناوين الأعمدة (لازم تكون بنفس ترتيب دالة الـ Import)
            fputcsv($handle, ['Name', 'Email', 'Role (student/doctor/ta)', 'Year (1-4)', 'Department (general/software/network)', 'Password (Optional)']);

            // 2. صف مثال توضيحي (عشان يعرف الصيغة)
            fputcsv($handle, ['Ahmed Ali', 'ahmed@example.com', 'student', '1', 'general', '12345678']);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    // 📤 دالة تصدير المستخدمين المحددين (Export Selected to CSV)
    public function exportSelectedUsers(Request $request)
    {
        $request->validate(['selected_ids' => 'required|string']);

        $ids = explode(',', $request->selected_ids);
        $users = User::whereIn('id', $ids)->get();

        $filename = "users_export_" . date('Y-m-d_H-i') . ".csv";

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');

            // 🔥 إضافة BOM عشان العربي يظهر صح في الإكسيل
            fputs($handle, "\xEF\xBB\xBF");

            // 1. عناوين الأعمدة (Header)
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Year', 'Department', 'Created By', 'Joined Date']);

            // 2. تعبئة البيانات
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    $user->role == 'student' ? $user->academic_year : 'N/A', // السنة لو طالب
                    ucfirst($user->department),
                    $user->creator ? $user->creator->name : 'System',
                    $user->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }


    // ... باقي الدوال فوق (import, destroy, etc)

    // 📥 دالة تصدير قاعدة البيانات (Backup)
    public function exportDatabase()
    {
        if (!Auth::user()->hasPermission('backup_db')) {
            abort(403, '⛔ ACCESS DENIED: You do not have permission to download the database.');
        }
        // 1. اسم الملف بالتاريخ والوقت عشان يبقى مميز
        $filename = "batu_backup_" . date('Y-m-d_H-i-s') . ".sql";

        // 2. تجهيز الهيدرز عشان المتصفح يفهم إنه ملف تحميل
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // 3. استخدام streamDownload عشان لو الداتا كبيرة متهنجش السيرفر
        return response()->streamDownload(function () {
            // فتح الاتصال
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            $dbName = \Illuminate\Support\Facades\DB::getDatabaseName();
            $tableProperty = "Tables_in_" . $dbName;

            // بداية ملف الـ SQL
            echo "-- BATU LMS Database Backup\n";
            echo "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            echo "-- --------------------------------------------------------\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n"; // تعطيل فحص العلاقات مؤقتاً

            foreach ($tables as $tableObj) {
                $table = $tableObj->$tableProperty;

                // أ. هيكل الجدول (Structure)
                $createTable = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `$table`");
                echo "-- Table structure for table `$table`\n";
                echo "DROP TABLE IF EXISTS `$table`;\n";
                echo $createTable[0]->{'Create Table'} . ";\n\n";

                // ب. البيانات (Data)
                echo "-- Dumping data for table `$table`\n";
                $rows = \Illuminate\Support\Facades\DB::table($table)->get();

                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $values[] = "NULL";
                            } elseif (is_numeric($value)) {
                                $values[] = $value;
                            } else {
                                // تنظيف النصوص من أي علامات تكسر الكود
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        echo "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
                echo "\n\n";
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n"; // إعادة تفعيل العلاقات
            echo "-- Backup Completed";
        }, $filename, $headers);
    }

    // ⚡ دالة التعديل الجماعي (Bulk Update)
    public function bulkUpdate(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'selected_ids' => 'required|string', // الـ IDs هتيجي مفصولة بفاصلة "1,2,5"

            // بيانات الستاف (اختياري)
            'courses' => 'nullable|array',
            'permissions' => 'nullable|array',

            // بيانات الطلبة (اختياري)
            'academic_year' => 'nullable|integer',
            'department' => 'nullable|string',
        ]);

        // تحويل النص لمصفوفة
        $ids = explode(',', $request->selected_ids);

        // 2. نجيب اليوزرز المحددين
        $users = User::whereIn('id', $ids)->get();

        foreach ($users as $user) {

            // 🛑 تخطي السوبر أدمن من التعديل الجماعي (أمان)
            if ($user->role === 'admin' && $user->id === 1) continue;

            // ==========================
            // A. لو طالب (Student Logic)
            // ==========================
            if ($user->role === 'student') {
                $updated = false;

                // تحديث السنة الدراسية (فقط لو تم اختيارها)
                if ($request->filled('academic_year')) {
                    $user->academic_year = $request->academic_year;
                    $updated = true;
                }

                // تحديث القسم (فقط لو تم اختياره)
                if ($request->filled('department')) {
                    $user->department = $request->department;
                    $updated = true;
                }

                // لو حصل أي تغيير في بيانات الطالب -> احفظ وأعد توزيع المواد
                if ($updated) {
                    $user->save();
                    // 🔥 دالة السحر: بتظبط المواد حسب السنة والقسم الجداد
                    $this->assignStudentCourses($user);
                }
            }

            // ==========================
            // B. لو ستاف (Staff Logic: Doctor, TA, Admin)
            // ==========================
            elseif (in_array($user->role, ['doctor', 'ta', 'admin'])) {

                // 1. تحديث الصلاحيات
                // (نستخدم has عشان لو المصفوفة فاضية معناها شال كل الصلاحيات)
                if ($request->has('permissions')) {
                    $newPermissions = $request->permissions ?? [];

                    // 🔥 تطبيق لوجيك الحماية (backup_db)
                    if (!Auth::user()->hasPermission('backup_db')) {
                        // لو أنا معيش الصلاحية، ومحاول أديها لحد -> شيلها
                        $newPermissions = array_diff($newPermissions, ['backup_db']);

                        // بس لو اليوزر الأصلي كان معاه الصلاحية -> سيبها معاه (منسحبهاش بالغلط)
                        if ($user->hasPermission('backup_db')) {
                            $newPermissions[] = 'backup_db';
                        }
                    }

                    // حفظ الصلاحيات (array_values عشان الترتيب)
                    $user->permissions = array_values(array_unique($newPermissions));
                    $user->save();
                }

                // 2. تحديث الكورسات اليدوية (للستاف فقط)
                if ($request->has('courses')) {
                    $user->courses()->sync($request->courses);
                }
            }
        }

        return back()->with('success', count($users) . ' Users updated successfully! 🚀');
    }

    // ⚡ دالة الحذف الجماعي (نقل للسلة - Soft Delete)
    public function bulkDelete(Request $request)
    {
        $request->validate(['selected_ids' => 'required|string']);

        $ids = explode(',', $request->selected_ids);

        // هات اليوزرز دول
        $users = User::whereIn('id', $ids)->get();

        foreach ($users as $user) {
            // 🛑 حماية: ممنوع حذف السوبر أدمن
            if ($user->id === 1) continue;

            // تسجيل مين اللي مسح
            $user->deleted_by_id = Auth::id();
            $user->saveQuietly(); // ✅ كده هيحفظ من غير ما يسجل Log (Updated)

            $user->delete(); // وهيسجل دي بس (Deleted)
        }

        return back()->with('success', count($users) . ' Users moved to trash successfully! 🗑️');
    }


    // ⚡ دالة التعامل الجماعي مع المحذوفات (Restore / Force Delete)
    public function bulkTrashAction(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|string',
            'action' => 'required|in:restore,force_delete'
        ]);

        $ids = explode(',', $request->selected_ids);

        // بنبحث في المحذوفين فقط
        $users = User::onlyTrashed()->whereIn('id', $ids)->get();

        if ($request->action === 'restore') {
            foreach ($users as $user) {
                $user->deleted_by_id = null; // تنظيف الأثر
                $user->save();
                $user->restore();
            }
            $message = count($users) . ' Users restored successfully! ♻️';
        } else { // force_delete
            foreach ($users as $user) {
                $user->forceDelete();
            }
            $message = count($users) . ' Users permanently deleted! 🚫';
        }

        return back()->with('success', $message);
    }

    // 🕵️‍♂️ دالة عرض سجل النشاطات
    public function activityLogs()
    {
        // بنجيب السجلات مع بيانات الفاعل (causer) والمفعول به (subject)
        $logs = \App\Models\ActivityLog::with(['causer', 'subject'])
            ->latest()
            ->paginate(20); // 20 سجل في الصفحة

        return view('admin.activity_logs', compact('logs'));
    }

    // 💀 دالة الحذف النهائي الجماعي
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string', // الـ IDs بتيجي مفصولة بفاصلة "1,2,5"
        ]);

        $ids = explode(',', $request->ids);

        // بنجيب اليوزرز من التراش (المحذوفين بس)
        $users = User::onlyTrashed()->whereIn('id', $ids)->get();

        $count = 0;
        foreach ($users as $user) {
            // تحقق إن اليوزر ده "من حقي" أمسحه (لو مش أدمن)
            if (Auth::user()->role !== 'admin' && $user->deleted_by_id !== Auth::id()) {
                continue; // فوت اليوزر ده ومتمسحوش
            }

            $user->forceDelete(); // 💀 مسح نهائي
            $count++;
        }

        return redirect()->back()->with('success', "$count Users have been PERMANENTLY deleted.");
    }
}
