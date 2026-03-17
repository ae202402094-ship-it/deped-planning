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
     */
    public function dashboard(Request $request)
    {
        // Start a query builder for Users
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
            // We replaced recentUsers with paginated $users so the search bar works!
            'users'        => $query->latest()->paginate(10)->withQueryString(), 
        ]);
    }

    /**
     * Update User Role & Status from Dashboard
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Capture old data before updating for the log
        $oldRole = $user->role;
        $oldStatus = $user->status;
        
        // Update the user's data
        $user->update([
            'role'   => $request->role,
            'status' => $request->status,
        ]);

        // Record this action in the Super Admin history
        $this->logAction('Updated User Account', $user->name, [
            'role' => "Changed from {$oldRole} to {$user->role}",
            'status' => "Changed from {$oldStatus} to {$user->status}"
        ]);

        return back()->with('success', "{$user->name}'s account has been successfully updated.");
    }

    /**
     * Notification Center (Pending Requests)
     */
    public function notifications()
    {
        // Fetch only users waiting for approval
        $notifications = User::where('status', 'pending')->latest()->get();
        return view('admin.pending_approvals', compact('notifications'));
    }

    /**
     * System History (Audit Log of Super Admin Actions)
     */
   /**
     * System History (Audit Log of ALL Actions for Super Admin)
     */
    public function history(Request $request)
    {
        // 1. Fetch ALL logs (Admin AND Super Admin)
        $query = ActivityLog::with('user')->latest();

        // 2. Omni-Search Logic (Searches Action, Target, or User Name)
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

        // 3. Date Range Filter
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
     * Action: Approve User
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        // Send the email notification
        $user->notify(new AccountApproved());

        // RECORD THIS ACTION FOR THE HISTORY TABLE
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
        
        // Store details before deleting so we can log them
        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->delete(); // Removes the pending request entirely

        // RECORD THIS ACTION FOR THE HISTORY TABLE
        $this->logAction('Rejected & Deleted User Request', $userName, [
            'email' => $userEmail,
            'status' => 'Account request deleted permanently'
        ]);

        return back()->with('error', "Request for {$userName} has been rejected and deleted.");
    }

    /**
     * PRIVATE HELPER: Record actions to the activity_logs table
     */
    private function logAction($action, $targetName, $changes = [])
    {
        ActivityLog::create([
            'user_id' => Auth::id(), // Uses imported Auth facade
            'action' => $action,
            'target_name' => $targetName,
            'changes' => $changes
        ]);
    }
}