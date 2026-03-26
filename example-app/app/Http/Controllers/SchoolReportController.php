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

    public function dataHealthReport()
{
    $schools = School::all();
    $flaggedSchools = [];

    foreach ($schools as $school) {
        $issues = [];

        // 1. Automated Infrastructure Health Check
        if ($school->no_of_classrooms > 0 && ($school->no_of_enrollees / $school->no_of_classrooms) > 50) {
            $issues[] = "Infrastructure: High Congestion (Over 50 students/classroom).";
        }
        if ($school->no_of_enrollees > 0 && $school->no_of_toilets == 0) {
            $issues[] = "Sanitation: Critical (0 toilets reported).";
        }

        // 2. Hazard Identification
        if ($school->hazard_landslide === 'High') {
            $issues[] = "Hazard: High Landslide Susceptibility.";
        }
        if ($school->hazard_flood === 'High') {
            $issues[] = "Hazard: High Flood Risk Area.";
        }
        if ($school->hazard_traffic === 'High') {
            $issues[] = "Hazard: Dangerous Traffic/Road Safety Risk.";
        }

        if (!empty($issues)) {
            $flaggedSchools[] = [
                'school' => $school,
                'issues' => $issues
            ];
        }
    }

    return view('admin.data_health', compact('flaggedSchools'));
}

}