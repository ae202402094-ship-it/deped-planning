<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class PublicSchoolController extends Controller
{
    /**
     * Display the list of schools with Search and Filter logic.
     * Maps to the 'public.schools' route.
     */
    public function listSchools(Request $request)
    {
        $query = School::query();

        // 1. Handle Search (Name or School ID)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('school_id', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Handle District Filter
        if ($request->filled('district') && $request->district !== 'All Districts') {
            $query->where('district', $request->district);
        }

        // 3. Paginate results updated to 10
        // withQueryString() ensures filters stay active when clicking next page
        $schools = $query->latest()->paginate(10)->withQueryString();

        return view('schools_list', compact('schools'));
    }

    /**
     * Display the specific details for one school.
     */
    public function showPublicDetail($id) 
    {
        $school = School::findOrFail($id); 
        
        return view('user_view', compact('school'));
    }
}