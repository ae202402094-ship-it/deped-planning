<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = School::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('school_id', 'like', '%' . $request->search . '%');
            });
        }

        $schools = $query->latest()->paginate(10)->withQueryString();

        return view('admin.schools', compact('schools'));
    }

    public function adminDashboard()
    {
        // 1. Total Registered Schools
        $schoolCount = School::count();
        
        // 2. Actionable Shortage KPIs (Fetch the actual lists)
        $classroomShortageSchools = School::where('classroom_shortage', '>', 0)
                                          ->get(['school_id', 'name', 'classroom_shortage']);
                                          
        $toiletShortageSchools = School::where('toilet_shortage', '>', 0)
                                       ->get(['school_id', 'name', 'toilet_shortage']);
        
        // 3. Hazard Risk (Fetch the actual list)
        $highHazardSchools = School::where('hazard_level', 'High')
                                   ->get(['school_id', 'name', 'hazard_type', 'hazard_level']);
        
        // 4. Electricity (Count for positive, fetch list for negative)
        $withPowerCount = School::where('with_electricity', true)->count();
        $withoutPowerSchools = School::where(function($q) {
            $q->where('with_electricity', false)->orWhereNull('with_electricity');
        })->get(['school_id', 'name']);
        
        // 5. Water (Count for positive, fetch list for negative)
        $withWaterCount = School::where('with_potable_water', true)->count();
        $withoutWaterSchools = School::where(function($q) {
            $q->where('with_potable_water', false)->orWhereNull('with_potable_water');
        })->get(['school_id', 'name']);

        // 6. System Activity & Records
        $archivedSchoolsCount = School::onlyTrashed()->count();
        $totalActivityLogs = ActivityLog::count();

        // Fetch schools with coordinates for the dashboard map
        $mapSchools = School::whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->get(['name', 'school_id', 'latitude', 'longitude', 'hazard_level']);

        return view('admin.dashboard', compact(
            'schoolCount', 
            'classroomShortageSchools',
            'toiletShortageSchools',
            'highHazardSchools',
            'withPowerCount',
            'withoutPowerSchools',
            'withWaterCount',
            'withoutWaterSchools',
            'archivedSchoolsCount',
            'totalActivityLogs',
            'mapSchools'
        ));
    }
}