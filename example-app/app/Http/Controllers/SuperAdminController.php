<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School; 
use Illuminate\Http\Request;
use App\Notifications\AccountApproved;

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
        
        // Update the user's data
        $user->update([
            'role'   => $request->role,
            'status' => $request->status,
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
        return view('admin.pending_approvals', compact('notifications')); // Adjust view name if needed
    }

    /**
     * System History (Audit Log of Users)
     */
    public function history()
    {
        // Show all users and their current status as a history log
        $history = User::latest('updated_at')->paginate(10);
        return view('admin.history', compact('history'));
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

        return back()->with('success', "Access granted for {$user->name}. Notification sent.");
    }

    /**
     * Action: Reject/Delete Request
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Removes the pending request entirely

        return back()->with('error', "Request for {$user->name} has been rejected and deleted.");
    }
}