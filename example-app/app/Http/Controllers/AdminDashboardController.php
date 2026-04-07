<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * This handles the main "Manage Schools" page (admin.schools route)
     */
    public function index(Request $request)
    {
        $query = School::query();

        // Maintain Search functionality
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('school_id', 'like', '%' . $request->search . '%');
            });
        }

        // Apply pagination limit of 10
        $schools = $query->latest()->paginate(10)->withQueryString();

        return view('admin.schools', compact('schools'));
    }

    /**
     * This handles the main Dashboard stats
     */
    public function adminDashboard()
    {
        $schoolCount = School::count();
        $teacherCount = School::sum('no_of_teachers');
        $totalEnrollees = School::sum('no_of_enrollees');
        $totalClassrooms = School::sum('no_of_classrooms');
        $totalToilets = School::sum('no_of_toilets');

        // Also paginating pending users to 10 for consistency
        $pendingUsers = User::where('status', 'pending')->latest()->paginate(10);

        return view('admin.dashboard', [
            'schoolCount' => $schoolCount,
            'teacherCount' => $teacherCount,
            'totalEnrollees' => $totalEnrollees,
            'totalClassrooms' => $totalClassrooms,
            'totalToilets' => $totalToilets,
            'pendingUsers' => $pendingUsers, 
        ]);
    }
}