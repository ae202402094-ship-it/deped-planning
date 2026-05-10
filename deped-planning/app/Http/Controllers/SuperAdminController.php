<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School; 
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Notifications\AccountApproved;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Super Admin Dashboard Overview
     * Shows statistics and provides a paginated, searchable user management list.
     */
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

    /**
     * Update User Role & Status from Dashboard
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $oldRole = $user->role;
        $oldStatus = $user->status;
        
        $user->update([
            'role'   => $request->role,
            'status' => $request->status,
        ]);

        $this->logAction('Updated User Account', $user->name, [
            'role' => "Changed from {$oldRole} to {$user->role}",
            'status' => "Changed from {$oldStatus} to {$user->status}"
        ]);

        return back()->with('success', "{$user->name}'s account has been successfully updated.");
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