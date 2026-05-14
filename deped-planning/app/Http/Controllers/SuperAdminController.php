<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School; 
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Notifications\AccountApproved;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Super Admin Dashboard Overview
     * Shows statistics and provides a paginated, searchable user management list.
     */

public function approvePasswordChange($id)
{
    $user = User::findOrFail($id);

    // Check if there is actually a pending password to move
    if ($user->pending_password) {
        // 1. Move the hashed pending password to the active password column
        $user->password = $user->pending_password;
        
        // 2. Clear the pending column so the "Approve" button disappears
        $user->pending_password = null; 
        
        $user->save();

        return back()->with('success', "Password for {$user->name} has been officially updated.");
    }

    return back()->with('error', 'No pending password change found for this user.');
}

// app/Http/Controllers/SuperAdminController.php

public function adminResetPassword(Request $request, $id)
{
    $request->validate([
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $user = User::findOrFail($id);
    
    // Instead of updating 'password' directly, we put it in 'pending_password'
    // This allows for the "Approval" step you mentioned.
    $user->pending_password = Hash::make($request->password);
    $user->save();

    return back()->with('success', "Password change initiated. Please click 'Approve' in the user table to finalize.");
}

    public function dashboard(Request $request)
    {
        $query = User::query();

        // 1. Search by Name or Email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter by Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 3. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('admin.super_dashboard', [
            'totalUsers'   => User::count(),
            'pendingCount' => User::where('status', 'pending')->count(),
            'adminCount'   => User::where('role', 'admin')->count(),
            'totalSchools' => School::count(), 
            'users'        => $query->latest()->paginate(10)->withQueryString(), 
        ]);
    }

    public function createUser()
{
    return view('admin.super_create_user'); // We will create this view next
}

public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:admin,super_admin',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'status' => 'approved', // Auto-approve since IT is creating it
        'email_verified_at' => now(), // Skip verification for manual provisioning
    ]);

    return redirect()->route('superadmin.history')->with('success', 'New Administrator account created successfully.');
}

    /**
     * Update User Role & Status from Dashboard
     */
   public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    // 1. SELF-PROTECTION LOCK
    // If the ID being updated is the same as the Logged-in Super Admin, 
    // block the update to prevent accidental self-demotion.
    if (auth()->id() == $user->id) {
        return back()->with('error', 'Critical Safety: You cannot modify your own administrative role or status from this panel.');
    }

    $request->validate([
        'role' => 'sometimes|in:admin,super_admin',
        'status' => 'sometimes|in:pending,approved,rejected',
    ]);

    // 2. EXPLICIT ASSIGNMENT
    // We only update if the role is present AND different from current
    if ($request->filled('role')) {
        $user->role = $request->role;
    }

    if ($request->filled('status')) {
        $user->status = $request->status;
    }

    $user->save();

    return back()->with('success', "Updated {$user->name} successfully.");
}
    /*
    |--------------------------------------------------------------------------
    | Account Approvals & Notifications
    |--------------------------------------------------------------------------
    */

    /**
     * Notification Center (Pending Requests)
     */
    public function notifications()
    {
        $notifications = User::where('status', 'pending')->latest()->get();
        return view('admin.pending_approvals', compact('notifications'));
    }

    /**
     * Action: Approve User
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        $user->notify(new AccountApproved());

        $this->logAction('Approved Account Request', $user->name, [
            'status' => 'Changed from pending to approved',
            'email' => $user->email
        ]);

        return back()->with('success', "Access granted for {$user->name}. Notification sent.");
    }

    /**
     * Action: Reject User
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->delete();

        $this->logAction('Rejected & Deleted User Request', $userName, [
            'email' => $userEmail,
            'status' => 'Account request deleted permanently'
        ]);

        return back()->with('error', "Request for {$userName} has been rejected and deleted.");
    }

    /*
    |--------------------------------------------------------------------------
    | Archiving & Data Restoration
    |--------------------------------------------------------------------------
    */

    /**
     * Action: View Archived Schools
     */
    public function archive()
    {
        $archivedSchools = School::onlyTrashed()->latest('deleted_at')->get();
        return view('admin.archive', compact('archivedSchools'));
    }


    public function forceDeleteBatch(Request $request) 
{
    if ($request->scope === 'all') {
        // Force delete every single trashed school
        School::onlyTrashed()->forceDelete();
        return back()->with('success', 'Archive has been completely wiped.');
    }

    // Otherwise, delete only the checked IDs
    School::onlyTrashed()->whereIn('id', $request->ids)->forceDelete();
    return back()->with('success', 'Selected records have been purged.');
}
    /**
     * Action: Restore Deleted School
     */
    public function restoreSchool($id)
    {
        $school = School::withTrashed()->findOrFail($id);
        $school->restore();

        $this->logAction('Restored School', $school->name, [
            'status' => 'Restored from archived records'
        ]);

        return back()->with('success', "{$school->name} has been successfully restored.");
    }

    /**
     * Action: Permanently Delete School from Archive
     */
    public function forceDeleteSchool($id)
    {
        $school = School::withTrashed()->findOrFail($id);
        $schoolName = $school->name;

        $school->forceDelete();

        $this->logAction('Permanently Purged School', $schoolName, [
            'status' => 'Record erased from database'
        ]);

        return back()->with('success', "{$schoolName} has been permanently removed from the system.");
    }

    /*
    |--------------------------------------------------------------------------
    | System Oversight & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * System History
     * Audit Log of ALL actions performed by Admins and Super Admins.
     */
    public function history(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('target_name', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(40)->withQueryString();

        return view('admin.super_history', compact('logs'));
    }

    /**
     * PRIVATE HELPER: Record actions to the activity_logs table
     */
    private function logAction($action, $targetName, $changes = [])
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_name' => $targetName,
            'changes' => $changes
        ]);
    }
}