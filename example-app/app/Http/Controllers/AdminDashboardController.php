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

        return view('admin.dashboard', compact('dashboardData', 'schools'));
    }

    public function adminDashboard()
    {
        // 1. Total Registered Schools
        $schoolCount = School::count();
        
        // 2. Actionable Shortage KPIs
        $classroomShortageSchools = School::where('classroom_shortage', '>', 0)
                                          ->get(['id', 'school_id', 'name', 'classroom_shortage']);
                                          
        $toiletShortageSchools = School::where('toilet_shortage', '>', 0)
                                       ->get(['id', 'school_id', 'name', 'toilet_shortage']);
        
        // 3. Hazard Risk (FIXED: Now checks hazard_type instead of hazard_level)
        $highHazardSchools = School::whereNotNull('hazard_type')
                                   ->where('hazard_type', '!=', 'None')
                                   ->get(['id', 'school_id', 'name', 'hazard_type']);
        
        // 4. Electricity
        $withPowerCount = School::where('with_electricity', 'Grid Connection')
                                ->orWhere('with_electricity', 'Solar Powered')
                                ->orWhere('with_electricity', 'Generator')
                                ->orWhere('with_electricity', 'Hybrid')
                                ->count();
                                
        $withoutPowerSchools = School::where(function($q) {
            $q->where('with_electricity', 'None')->orWhereNull('with_electricity');
        })->get(['id', 'school_id', 'name']);
        
        // 5. Water
        $withWaterCount = School::where('with_potable_water', true)->count();
        $withoutWaterSchools = School::where(function($q) {
            $q->where('with_potable_water', false)->orWhereNull('with_potable_water');
        })->get(['id', 'school_id', 'name']);

        // 6. System Activity & Records
        $archivedSchoolsCount = School::onlyTrashed()->count();
        $totalActivityLogs = ActivityLog::count();

        // Fetch schools with coordinates for the dashboard map (FIXED: Added hazard_type)
        $mapSchools = School::whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->get(['id', 'name', 'school_id', 'latitude', 'longitude', 'hazard_type']);

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