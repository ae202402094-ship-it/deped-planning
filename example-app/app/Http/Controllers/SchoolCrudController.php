<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ActivityLog;

class SchoolCrudController extends Controller
{
    public function manageSchools(Request $request) 
    {
        $search = $request->input('search');

        $schools = School::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('school_id', 'like', "%{$search}%");
        })->orderBy('name', 'asc')->paginate(10); 

        return view('admin.schools', compact('schools'));
    }

    public function createSchool()
    {
        return view('admin.create_school');
    }

    public function storeSchool(Request $request)
    {
        $validated = $request->validate([
            'school_id' => 'required|unique:schools,school_id',
            'name' => 'required|string|max:255|unique:schools,name',
            'no_of_teachers' => 'required|integer|min:0',
            'no_of_enrollees' => 'required|integer|min:0',
            'no_of_classrooms' => 'required|integer|min:0',
            'no_of_toilets' => 'required|integer|min:0',
            'hazard_type' => 'required|string',
            'hazard_level' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Logic for "Others" hazard type
        if ($request->hazard_type === 'Others' && $request->filled('hazard_others')) {
            $validated['hazard_type'] = $request->hazard_others;
        }

        try {
            $school = School::create($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Created New School',
                'target_name' => $school->name,
                'changes' => ['after' => $school->toArray()]
            ]);

            return redirect()->route('admin.schools')->with('success', 'New school registered successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database Error: Could not save school.');
        }
    }

    public function editSchool($id)
    {
        $school = School::findOrFail($id);
        return view('admin.edit_school', compact('school'));
    }

   public function updateSchool(Request $request, $id)
{
    $school = School::findOrFail($id);
    $oldData = $school->toArray();

    $validated = $request->validate([
        'school_id' => 'required|unique:schools,school_id,' . $school->id,
        'name' => 'required|string|max:255',
        'no_of_teachers' => 'required|integer|min:0',
        'no_of_enrollees' => 'required|integer|min:0',
        'no_of_classrooms' => 'required|integer|min:0',
        'no_of_chairs' => 'required|integer|min:0',
        'no_of_toilets' => 'required|integer|min:0',
        'with_electricity' => 'required|string',
        'with_potable_water' => 'required|boolean',
        'with_internet' => 'required|boolean',
        'classroom_shortage' => 'nullable|integer|min:0',
        'chair_shortage' => 'nullable|integer|min:0',
        'toilet_shortage' => 'nullable|integer|min:0',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'hazard_type' => 'required|string', // Capture the dropdown
        'hazards' => 'nullable|string',     // Capture the textarea
    ]);

    if ($request->hazard_type !== 'Others') {
        $validated['hazards'] = $request->hazard_type;
    }
    
    // Perform the update
    $school->update($validated);

    // Activity Logging
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Updated School Profile',
        'target_name' => $school->name,
        'changes' => [
            'before' => $oldData,
            'after' => $school->fresh()->toArray()
        ]
    ]);

    return redirect()->route('schools.edit', $school->id)
        ->with('success', 'Registry synchronized and updated successfully.');
}
    public function destroySchool($id)
    {
        try {
            $school = School::findOrFail($id);
            $name = $school->name;
            $oldData = $school->toArray();
            
            $school->delete();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Deleted School',
                'target_name' => $name,
                'changes' => ['before' => $oldData]
            ]);

            return redirect()->route('admin.schools')->with('success', "{$name} has been moved to archives.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'System Error: Deletion failed.');
        }
    }

    public function restoreSchool($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        $school->restore();
        return redirect()->route('schools.archive')->with('success', "{$school->name} reinstated.");
    }

    public function forceDeleteSchool($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        $name = $school->name;
        $school->forceDelete();
        return redirect()->route('schools.archive')->with('success', "{$name} permanently purged.");
    }

    public function archivedSchools(Request $request) 
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');
        $schools = School::onlyTrashed()
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('school_id', 'like', "%{$search}%");
                });
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10); 

        return view('admin.schools_archive', compact('schools'));
    }

    public function checkDuplicate(Request $request)
    {
        $field = $request->input('field'); 
        $value = $request->input('value');
        $excludeId = $request->input('exclude_id'); 
        $query = School::where($field, $value);
        if ($excludeId) { $query->where('id', '!=', $excludeId); }
        return response()->json(['exists' => $query->exists()]);
    }

    public function manageTeachers(Request $request) 
    {
        // ... (Keep your existing manageTeachers logic here, it looked functional)
        $schools = School::all();
        $totalTeachers = $schools->sum('no_of_teachers');
        return view('admin.teachers', compact('schools', 'totalTeachers'));
    }


    public function audit()
{
    $schools = School::all();
    $flaggedSchools = [];

    foreach ($schools as $school) {
        $issues = [];

        // 1. Math Check (Ratios)
        if ($school->no_of_teachers > 0 && ($school->no_of_enrollees / $school->no_of_teachers) > 45) {
            $issues[] = "Critical Teacher-Learner Ratio";
        }
        if ($school->no_of_classrooms > 0 && ($school->no_of_enrollees / $school->no_of_classrooms) > 45) {
            $issues[] = "Severe Classroom Overcrowding";
        }

        // 2. Physical Hazard Check
        $hasPhysicalHazard = ($school->hazard_type && $school->hazard_type !== 'None');

        // Flag the school if it has ANY issue OR a physical hazard
        if (!empty($issues) || $hasPhysicalHazard) {
            $flaggedSchools[] = [
                'school' => $school,
                'issues' => $issues
            ];
        }
    }

    return view('admin.audit', compact('flaggedSchools'));
}


    
}