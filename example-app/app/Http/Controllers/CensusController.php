<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;

class CensusController extends Controller
{

    public function showMap() {
    $schools = School::all(); // Fetch all schools with their coordinates
    return view('admin.map', compact('schools'));
}

/**
 * PUBLIC: Show the interactive map for all schools.
 */
public function showPublicMap()
{
    // Fetch all schools to plot them as markers on the map
    $schools = School::all(); 
    return view('public_map', compact('schools'));
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
// app/Http/Controllers/CensusController.php
public function showPublic($id) // Change from (Request $request)
{
    $school = School::findOrFail($id); // Laravel now passes the ID directly
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

    // app/Http/Controllers/CensusController.php

public function updateSchool(Request $request, $id)
{
    // Secure Validation: The 'unique' rule now ignores the current record's ID
    $validated = $request->validate([
        'school_id' => 'required|string|unique:schools,school_id,' . $id,
        'name' => 'required|string|max:255|unique:schools,name,' . $id,
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
    ]);

    try {
        $school = School::findOrFail($id);
        // Use $validated instead of $request->all() to prevent mass assignment injection
        $school->update($validated); 

        return redirect()->route('admin.schools')
            ->with('success', "Registry Synchronization Complete for {$school->name}.");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'System Error: Update protocol failed.');
    }
}

public function checkDuplicate(Request $request)
{
    $field = $request->input('field'); // 'school_id' or 'name'
    $value = $request->input('value');
    $excludeId = $request->input('exclude_id'); // From your Edit page's JS

    $query = School::where($field, $value);

    // If on edit page, ignore the record currently being edited
    if ($excludeId) {
        $query->where('id', '!=', $excludeId);
    }

    return response()->json(['exists' => $query->exists()]);
}

public function destroySchool($id)
{
    try {
        $school = School::findOrFail($id);
        $name = $school->name;
        $school->delete();

        return redirect()
            ->route('admin.schools')
            ->with('success', "Protocol Complete: {$name} has been purged from the registry.");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'System Error: Deletion protocol failed.');
    }
}

/**
 * ADMIN: Show the dedicated registration page.
 */
public function createSchool()
{
    return view('admin.create_school');
}

    /**
     * ADMIN: Save a new school with inventory data.
     */
    // app/Http/Controllers/CensusController.php

public function storeSchool(Request $request)
{
    $validated = $request->validate([
        // Checks 'schools' table to ensure both are unique before saving
        'school_id' => 'required|unique:schools,school_id',
        'name' => 'required|string|max:255|unique:schools,name',
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
    ]);

    try {
        School::create($validated);
        return redirect()->route('admin.schools')->with('success', 'New school registered successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Database Error: Could not save school.');
    }
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