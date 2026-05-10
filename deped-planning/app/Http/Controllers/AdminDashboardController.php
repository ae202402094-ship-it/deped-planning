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

        return view('admin.dashboard', compact('schools'));
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
        
        // 3. Hazard Risk (FIXED: Handles new JSON arrays and old strings safely)
        $highHazardSchools = School::whereNotNull('hazard_type')
                                   ->where('hazard_type', '!=', 'None')
                                   ->where('hazard_type', '!=', '["None"]')
                                   ->where('hazard_type', '!=', '[]')
                                   ->get(['id', 'school_id', 'name', 'hazard_type'])
                                   // Filter out empty arrays dynamically
                                   ->filter(function ($school) {
                                       $hazards = is_array($school->hazard_type) ? $school->hazard_type : json_decode($school->hazard_type, true);
                                       return !empty($hazards) && $hazards !== ['None'];
                                   });
        
        // 4. Electricity (FIXED: Added new 'Off-grid + Solar/Genset' combo)
        $withPowerCount = School::whereIn('with_electricity', [
            'Grid Connection', 
            'Hybrid', 
            'Off-grid + Solar/Genset',
            'Solar Powered', // Kept for legacy data safety
            'Generator'      // Kept for legacy data safety
        ])->count();
                                
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

        // 7. Geographic Map Data (FIXED: Strictly rejects empty strings so the JS map doesn't crash)
        $mapSchools = School::whereNotNull('latitude')
                            ->where('latitude', '!=', '')
                            ->whereNotNull('longitude')
                            ->where('longitude', '!=', '')
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