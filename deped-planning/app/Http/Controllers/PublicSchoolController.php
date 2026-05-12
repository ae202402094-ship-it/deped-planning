<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;

class PublicSchoolController extends Controller
{
    /**
     * 1. PUBLIC DIRECTORY LIST
     * Handles server-side filtering and pagination for schools_list.blade.php
     */
    public function listSchools(Request $request)
    {
        $search = $request->input('search');
        $sector = $request->input('sector');
        $level = $request->input('level');
        $district = $request->input('district');

        $schools = School::when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('school_id', 'like', "%{$search}%");
            });
        })
        ->when($sector, function ($query, $sector) {
            return $query->where('sector', $sector);
        })
        ->when($level, function ($query, $level) {
            return $query->where('school_level', $level);
        })
        ->when($district, function ($query, $district) {
            return $query->where('district', $district);
        })
        ->orderBy('name', 'asc')
        ->paginate(12); // Paginates 12 schools per page

        return view('schools_list', compact('schools'));
    }

    /**
     * 2. INTERACTIVE PUBLIC MAP
     * Passes all valid schools to public_map.blade.php for instant Javascript filtering
     */
    public function map()
    {
        // Only fetch schools that have coordinates so the map doesn't break
        $schools = School::whereNotNull('latitude')
                         ->whereNotNull('longitude')
                         ->get();

        return view('public_map', compact('schools'));
    }

    /**
     * 3. INDIVIDUAL SCHOOL PROFILE
     * Displays the detailed view for a single school in user_view.blade.php
     */
    public function showPublicDetail($id) // <-- Renamed to match your web.php route
    {
        $school = School::findOrFail($id);
        
        return view('user_view', compact('school'));
    }
}