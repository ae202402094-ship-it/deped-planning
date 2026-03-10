<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use App\Notifications\AccountApproved;

class AdminController extends Controller
{
    /**
     * Display the main Superadmin Dashboard
     */
    public function index()
    {
        // Fetch pending regular users (status 0 or 'pending')
        // Only show users who have already verified their email
        $pendingUsers = User::where('role', 'admin')
                            ->where('status', 'pending')
                            ->whereNotNull('email_verified_at')
                            ->get();

        $schoolCount = School::count(); 

        return view('admin.dashboard', compact('pendingUsers', 'schoolCount'));
    }

    /**
     * Approve a regular User account
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved'; 
        $user->save();

        // Notify the user via the Mailer
        $user->notify(new AccountApproved());
        
        return back()->with('success', 'User approved and notified successfully.');
    }

    /**
     * Reject/Delete a User or Admin request
     */
    public function reject($id)
    {
        $user = User::findOrFail($id);
        
        // Delete the user so they can attempt to register again if needed
        $user->delete(); 

        return back()->with('error', 'Account request rejected and removed.');
    }

    /**
     * Display the specialized page for Admin Account Approvals
     */
    public function pendingAdmins()
    {
        // Fetch only users with 'admin' role and 'pending' status
        $pendingAdmins = User::where('role', 'admin')
                             ->where('status', 'pending')
                             ->get();

        return view('admin.pending_approvals', compact('pendingAdmins'));
    }

    /**
     * Approve an Admin account
     */
    public function approveAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        // Notify the admin via Mailer that they can now log in
        $user->notify(new AccountApproved());

        return back()->with('success', 'Admin account approved successfully.');
    }
}