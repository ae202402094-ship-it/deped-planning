<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use App\Notifications\AccountApproved; // 1. Added this import

class AdminController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('role', 'user')
                            ->where('status', 0)
                            ->whereNotNull('email_verified_at')
                            ->get();

        $schoolCount = School::count(); 

        return view('admin.dashboard', compact('pendingUsers', 'schoolCount'));
    }

    // 2. Merged the two approve methods into this single one
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 1; // 1 = Approved
        $user->save();

        // Send the notification to the user
        $user->notify(new AccountApproved());
        
        return back()->with('success', 'User approved and notified successfully.');
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        
        // We delete the user so they can try to register again if it was a mistake
        $user->delete(); 

        return back()->with('error', 'User request rejected.');
    }
}