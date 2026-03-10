<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        'totalUsers'   => \App\Models\User::count(),
        'pendingCount' => \App\Models\User::where('status', 'pending')->count(),
        'adminCount'   => \App\Models\User::where('role', 'admin')->count(),
        'recentUsers'  => \App\Models\User::latest()->take(5)->get(),
    ]);
}

    /**
     * Notification Center (Pending Requests)
     */
    public function notifications()
    {
        // Fetch only users waiting for approval
        $notifications = User::where('status', 'pending')->latest()->get();
        return view('admin.notifications', compact('notifications'));
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

        return back()->with('error', "Request for {$user->name} has been rejected.");
    }
}