<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;

class CensusController extends Controller
{
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
    public function manageSchools() {
        $schools = School::all();
        return view('admin.schools', compact('schools'));
    }

    public function updateSchool(Request $request, $id)
{
    $school = School::findOrFail($id);
    
    // Validate that the numbers are at least 0
    $request->validate([
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
    ]);

    $school->update($request->only([
        'no_of_teachers', 
        'no_of_enrollees', 
        'no_of_classrooms', 
        'no_of_toilets'
    ]));

    return redirect()->back()->with('success', 'School inventory updated!');
}

    /**
     * ADMIN: Save a new school with inventory data.
     */
    public function storeSchool(Request $request)
{
    $validated = $request->validate([
        'school_id' => 'required|unique:schools,school_id',
        'name' => 'required|string|max:255',
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
    ]);

    School::create($validated);

    return redirect()->back()->with('success', 'School added successfully!');
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