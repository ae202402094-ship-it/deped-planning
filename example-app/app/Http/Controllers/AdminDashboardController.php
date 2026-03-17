<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function adminDashboard()
    {
        $schools = School::all();
        $pendingUsers = User::where('status', 'pending')->get(); 

        return view('admin.dashboard', [
            'schoolCount' => $schools->count(),
            'teacherCount' => $schools->sum('no_of_teachers'),
            'totalEnrollees' => $schools->sum('no_of_enrollees'),
            'totalClassrooms' => $schools->sum('no_of_classrooms'),
            'totalToilets' => $schools->sum('no_of_toilets'),
            'pendingUsers' => $pendingUsers, 
        ]);
    }
}