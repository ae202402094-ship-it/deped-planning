<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School; // Make sure to import School model
use Illuminate\Http\Request;
use App\Notifications\AccountApproved;

class SuperAdminController extends Controller
{
    /**
     * Super Admin Dashboard Overview
     */
    public function dashboard()
    {
        return view('admin.super_dashboard', [
            'totalUsers'   => User::count(),
            'pendingCount' => User::where('status', 'pending')->count(),
            'adminCount'   => User::where('role', 'admin')->count(),
            'recentUsers'  => User::latest()->take(5)->get(),
            'totalSchools' => School::count(), // Add total schools to dashboard
        ]);
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