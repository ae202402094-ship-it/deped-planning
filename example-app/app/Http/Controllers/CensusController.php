<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;

class CensusController extends Controller
{

    public function showMap() {
    $schools = School::all();
    return view('admin.map', compact('schools'));
}

    /**
     * PUBLIC: List all schools for the viewer directory.
     */
    public function listSchools()
    {
        $schools = School::all(); 
        return view('schools_list', compact('schools'));
    }

    /**
     * PUBLIC: Show the inventory details for a specific school.
     */
   public function showPublic(Request $request)
{
    $schoolId = $request->query('school_id');
    $school = School::findOrFail($schoolId); // Use findOrFail to catch errors

    return view('user_view', compact('school'));
}

    /**
     * ADMIN: Dashboard overview showing total counts for the division.
     */
  public function adminDashboard()
{
    $schools = School::all();

    return view('admin.dashboard', [
        'schoolCount' => $schools->count(),
        'teacherCount' => $schools->sum('no_of_teachers'),
        'totalEnrollees' => $schools->sum('no_of_enrollees'),
        'totalClassrooms' => $schools->sum('no_of_classrooms'),
        'totalToilets' => $schools->sum('no_of_toilets'),
    ]);
}

    /**
     * ADMIN: Display the list of schools for management.
     */
    /**
 * ADMIN: Display the list of schools for management with search.
 */
public function manageSchools(Request $request) {
    $search = $request->input('search');

    $schools = School::when($search, function ($query, $search) {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('school_id', 'like', "%{$search}%");
    })->get();

    return view('admin.schools', compact('schools'));
}

/**
 * ADMIN: Show the dedicated edit page for a specific school.
 */
public function editSchool($id) // Change from 'edit' to 'editSchool'
{
    $school = School::findOrFail($id);
    return view('admin.edit_school', compact('school'));
}

    public function updateSchool(Request $request, $id)
{
    // 1. Validate the input, including the new map coordinates
    $validated = $request->validate([
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
    ]);

    try {
        // 2. Locate the school record
        $school = School::findOrFail($id);

        // 3. Update using the validated data array
        $school->update($validated);

        // 4. Redirect with a professional success notice
        return redirect()
            ->route('admin.schools')
            ->with('success', "Registry updated: Assets and geolocation for {$school->name} are now live.");

    } catch (\Exception $e) {
        // 5. Catch database or connection errors
        return redirect()
            ->back()
            ->with('error', 'Critical System Error: Database was unable to commit the audit changes.');
    }
}

    /**
     * ADMIN: Save a new school with inventory data.
     */
    public function storeSchool(Request $request)
{
    // 1. Validate the input, including the new map coordinates from the registration form
    $validated = $request->validate([
        'school_id' => 'required|unique:schools,school_id',
        'name' => 'required|string|max:255',
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
        'latitude' => 'nullable|numeric|between:-90,90',  // Added for geolocation
        'longitude' => 'nullable|numeric|between:-180,180', // Added for geolocation
    ]);

    // 2. Create the school record with coordinates
    School::create($validated);

    // 3. Return to the registry with a success confirmation
    return redirect()->back()->with('success', 'New school profile and geographic location registered successfully!');
}

    /**
     * ADMIN: Manage individual school quantities.
     * Note: This replaces the old teacher rankings logic.
     */
    public function manageTeachers(Request $request) 
    {
        // Handle POST updates for school data
        if ($request->isMethod('post')) {
            if ($request->has('update_school')) {
                $school = School::find($request->id);
                $school->update([
                    'no_of_teachers' => $request->no_of_teachers,
                    'no_of_enrollees' => $request->no_of_enrollees,
                    'no_of_classrooms' => $request->no_of_classrooms,
                    'no_of_toilets' => $request->no_of_toilets,
                ]);
                return redirect()->back()->with('success', 'School inventory updated!');
            }

            if ($request->has('delete_school')) {
                School::destroy($request->id);
                return redirect()->back()->with('success', 'School deleted!');
            }
        }

        // GET: Fetch data for the management table
        $schools = School::all();
        $totalTeachers = $schools->sum('no_of_teachers');

        return view('admin.teachers', compact('schools', 'totalTeachers'));
    }
}