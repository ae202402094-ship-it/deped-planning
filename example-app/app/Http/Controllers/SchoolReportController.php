<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ActivityLog;

class SchoolReportController extends Controller
{
    public function generateReport($id)
    {
        $school = School::findOrFail($id);
        
        $ratios = [
            'teacher' => $school->no_of_teachers > 0 ? round($school->no_of_enrollees / $school->no_of_teachers) : 0,
            'classroom' => $school->no_of_classrooms > 0 ? round($school->no_of_enrollees / $school->no_of_classrooms) : 0,
        ];

        return view('admin.school_report', compact('school', 'ratios'));
    }

    public function viewHistory(Request $request)
    {
        $query = ActivityLog::whereHas('user', function($q) {
            $q->where('role', 'admin');
        })->with('user')->latest();

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

        return view('admin.history', compact('logs'));
    }
}