<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CensusController extends Controller
{

// app/Http/Controllers/CensusController.php
// app/Http/Controllers/CensusController.php
/**
 * DEBUG TOOL: Wipe the entire school registry.
 */
public function clearAllSchools()
{
    try {
        // truncate() resets the IDs and clears the table instantly
        \App\Models\School::truncate(); 
        
        return redirect()->route('admin.schools')
            ->with('success', 'DEBUG: School registry has been completely wiped.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'System Error: Wipe protocol failed.');
    }
}

public function confirmImport(Request $request)
{
    $data = session('pending_import');

    if (!$data) {
        return redirect()->route('admin.schools')->with('error', 'No pending data found or session expired.');
    }

    foreach ($data as $row) {
        // Explicitly cast values to ensure the DB recognizes them as numbers
        School::updateOrCreate(
            ['school_id' => (string)$row['school_id']], 
            [
                'name'             => (string)$row['name'],
                'no_of_teachers'   => (int)$row['no_of_teachers'],
                'no_of_enrollees'  => (int)$row['no_of_enrollees'],
                'no_of_classrooms' => (int)$row['no_of_classrooms'],
                'no_of_toilets'    => (int)$row['no_of_toilets'],
                'latitude'         => (float)$row['latitude'],
                'longitude'        => (float)$row['longitude'],
            ]
        );
    }

    session()->forget('pending_import');
    return redirect()->route('admin.schools')->with('success', 'Registry successfully synchronized.');
}

public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|mimes:csv,txt|max:2048'
    ]);

    $file = $request->file('csv_file');
    $handle = fopen($file->getRealPath(), 'r');
    fgetcsv($handle); // Skip header

    $formattedData = [];
    $newCount = 0;
    $updateCount = 0;
    $conflictCount = 0;
    $nameMismatchCount = 0; // Track name-ID mismatches
    $seenIds = []; 

    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (!empty($row[0])) {
            $currentId = (string)$row[0];
            $currentName = (string)($row[1] ?? 'N/A');
            
            // 1. Check if the ID exists
            $existingSchoolById = \App\Models\School::where('school_id', $currentId)->first();
            
            // 2. Check if the Name exists under a DIFFERENT ID
            $existingSchoolByName = \App\Models\School::where('name', $currentName)
                                    ->where('school_id', '!=', $currentId)
                                    ->first();

            if (isset($seenIds[$currentId])) {
                $status = 'conflict';
                $conflictCount++;
            } elseif ($existingSchoolByName) {
                // This is the new notification state you requested
                $status = 'name_mismatch'; 
                $nameMismatchCount++;
            } else {
                $status = $existingSchoolById ? 'update' : 'new';
                $existingSchoolById ? $updateCount++ : $newCount++;
            }

            $seenIds[$currentId] = true;

            $formattedData[] = [
                'school_id'        => $currentId,
                'name'             => $currentName,
                'no_of_teachers'   => (int)($row[2] ?? 0),
                'no_of_enrollees'  => (int)($row[3] ?? 0),
                'no_of_classrooms' => (int)($row[4] ?? 0),
                'no_of_toilets'    => (int)($row[5] ?? 0),
                'latitude'         => !empty($row[6]) ? (float)$row[6] : 6.9214,
                'longitude'        => !empty($row[7]) ? (float)$row[7] : 122.0739,
                'status'           => $status,
                'exists_in_db'     => (bool)$existingSchoolById,
                'mismatch_id'      => $existingSchoolByName ? $existingSchoolByName->school_id : null,
                'old_values'       => $existingSchoolById ? [
                    'no_of_teachers'  => $existingSchoolById->no_of_teachers,
                    'no_of_enrollees' => $existingSchoolById->no_of_enrollees,
                    'name'            => $existingSchoolById->name,
                ] : null,
            ];
        }
    }
    fclose($handle);

    session(['pending_import' => $formattedData]);
    
    // Pass nameMismatchCount to the view
    return view('admin.preview_import', compact(
        'formattedData', 
        'newCount', 
        'updateCount', 
        'conflictCount', 
        'nameMismatchCount'
    ));
}

public function downloadSampleCSV(): StreamedResponse
{
    return new StreamedResponse(function () {
        $handle = fopen('php://output', 'w');
        
        // Added latitude and longitude to the headers
        fputcsv($handle, [
            'school_id', 'name', 'no_of_teachers', 'no_of_enrollees', 
            'no_of_classrooms', 'no_of_toilets', 'latitude', 'longitude'
        ]);

        // Example row with Zamboanga coordinates
        fputcsv($handle, ['123456', 'SAMPLE SCHOOL', '20', '500', '15', '8', '6.9214', '122.0739']);

        fclose($handle);
    }, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="deped_census_map_template.csv"',
    ]);
}

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

        // Fetch users who are verified but not yet active/approved
        // Adjust 'status' or 'is_active' based on your actual database column
        $pendingUsers = User::where('status', 'pending')->get(); 

        return view('admin.dashboard', [
            'schoolCount' => $schools->count(),
            'teacherCount' => $schools->sum('no_of_teachers'),
            'totalEnrollees' => $schools->sum('no_of_enrollees'),
            'totalClassrooms' => $schools->sum('no_of_classrooms'),
            'totalToilets' => $schools->sum('no_of_toilets'),
            'pendingUsers' => $pendingUsers, // Pass the variable to the view
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

// app/Http/Controllers/CensusController.php

public function updateSchool(Request $request, $id)
{
    $validated = $request->validate([
        'school_id' => 'required|string|unique:schools,school_id,' . $id,
        'name' => 'required|string|max:255|unique:schools,name,' . $id,
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
        // Added range validation to prevent Map crashes
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
    ]);

    $school = School::findOrFail($id);
    $school->update($validated);

    return redirect()->route('admin.schools')->with('success', 'Registry updated successfully.');
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