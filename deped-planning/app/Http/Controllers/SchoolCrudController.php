<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class SchoolCrudController extends Controller
{
  public function manageSchools(Request $request) 
    {
        $search = $request->input('search');
        $sector = $request->input('sector');     // NEW
        $level = $request->input('level');       
        $district = $request->input('district'); 

        $schools = School::when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('school_id', 'like', "%{$search}%");
            });
        })
        ->when($sector, function ($query, $sector) {    // NEW
            return $query->where('sector', $sector);
        })
        ->when($level, function ($query, $level) {
            return $query->where('school_level', $level);
        })
        ->when($district, function ($query, $district) {
            return $query->where('district', $district);
        })
        ->orderBy('name', 'asc')->paginate(10); 

        $districts = School::select('district')->distinct()->whereNotNull('district')->pluck('district');

        return view('admin.schools', compact('schools', 'districts'));
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
            'school_level' => 'required|string',
            'district' => 'required|string',
            'no_of_teachers' => 'required|integer|min:0',
            'no_of_enrollees' => 'required|integer|min:0',
            'no_of_classrooms' => 'required|integer|min:0',
            'no_of_toilets' => 'required|integer|min:0',
            'no_of_chairs' => 'required|integer|min:0',
            'with_electricity' => 'required|string',
            'with_potable_water' => 'required|boolean',
            'with_internet' => 'required|boolean',
            'hazard_type' => 'nullable|array', 
            'hazard_level' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Process Hazards using your existing private helper
        $validated['hazard_type'] = $this->processHazards(
            $request->input('hazard_type', []),
            $request->input('custom_hazards', [])
        );

        // Calculate Ratios
        $validated['teacher_ratio'] = $validated['no_of_enrollees'] > 0 ? round($validated['no_of_enrollees'] / $validated['no_of_teachers'], 2) : 0;
        $validated['classroom_ratio'] = $validated['no_of_enrollees'] > 0 ? round($validated['no_of_enrollees'] / $validated['no_of_classrooms'], 2) : 0;

        try {
            // Create the record once and log it
            $school = School::create($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Created New School',
                'target_name' => $school->name,
                'changes' => ['after' => $school->toArray()]
            ]);

            return redirect()->route('admin.schools')->with('success', 'New school registered successfully!');
        } catch (\Exception $e) {
            // Include the error message to help you debug database issues
            return redirect()->back()->with('error', 'Database Error: ' . $e->getMessage());
        }
    } // This closing brace was missing
    public function editSchool($id)
    {
        $school = School::findOrFail($id);
        return view('admin.edit_school', compact('school'));
    }

    public function updateSchool(Request $request, $id)
{
    $school = School::findOrFail($id);
    $oldData = $school->toArray();

    $validatedData = $request->validate([
        'school_id' => 'required|string|max:255|unique:schools,school_id,' . $school->id,
        'name' => 'required|string|max:255',
        // Change from 'nullable' to 'required' and match your specific levels
        'school_level' => 'required|in:Elementary,Junior High School,Senior High School,Integrated',
        'district' => 'required|string|max:255',
        'no_of_teachers' => 'nullable|integer|min:0',
        'no_of_enrollees' => 'nullable|integer|min:0',
        'no_of_classrooms' => 'nullable|integer|min:0',
        'no_of_chairs' => 'nullable|integer|min:0',
        'no_of_toilets' => 'nullable|integer|min:0',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'with_electricity' => 'nullable|string',
        'with_potable_water' => 'nullable|boolean',
        'with_internet' => 'nullable|boolean',
        'hazard_type' => 'nullable|array',
    ]);

    // Re-calculate ratios before saving to keep KPIs accurate
    $validatedData['teacher_ratio'] = $validatedData['no_of_enrollees'] > 0 
        ? round($validatedData['no_of_enrollees'] / ($validatedData['no_of_teachers'] ?: 1), 2) 
        : 0;
    
    $validatedData['classroom_ratio'] = $validatedData['no_of_enrollees'] > 0 
        ? round($validatedData['no_of_enrollees'] / ($validatedData['no_of_classrooms'] ?: 1), 2) 
        : 0;

    // Process hazards using your helper
    $validatedData['hazard_type'] = $this->processHazards(
        $request->input('hazard_type', []),
        $request->input('custom_hazards', [])
    );

    $school->forceFill($validatedData)->save();

    // Log the activity
    if ($school->forceFill($validatedData)->save()) {
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Updated School Profile',
        'target_name' => $school->name,
        'changes' => [
            'before' => $oldData,
            'after' => $school->fresh()->toArray()
        ]
    ]);
}

    return redirect()->route('admin.schools')->with('success', 'Institutional record updated successfully.');
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

    public function archivedSchools(Request $request) 
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');
        
        $archivedSchools = School::onlyTrashed()
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('school_id', 'like', "%{$search}%");
                });
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10); 

        return view('admin.schools_archive', compact('archivedSchools'));
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

   public function forceDeleteBatch(Request $request) 
{
    // Ensure the user has the right permissions
    if (!in_array(auth()->user()->role, ['super_admin'])) {
        return back()->with('error', 'Unauthorized: Only Super Admins can purge records.');
    }

    try {
        // Handle "Delete All Archives"
        if ($request->scope === 'all') {
            School::onlyTrashed()->forceDelete();
            return redirect()->route('schools.archive')->with('success', 'The entire archive has been wiped.');
        }

        // Handle "Purge Selected"
        if ($request->filled('ids') && is_array($request->ids)) {
            School::onlyTrashed()->whereIn('id', $request->ids)->forceDelete();
            return redirect()->route('schools.archive')->with('success', 'Selected records purged successfully.');
        }

        return redirect()->route('schools.archive')->with('error', 'No records were selected.');

    } catch (\Exception $e) {
        return redirect()->route('schools.archive')->with('error', 'System Error: ' . $e->getMessage());
    }
}
    public function checkDuplicate(Request $request)
    {
        $field = $request->input('field'); 
        $value = $request->input('value');
        $excludeId = $request->input('exclude_id'); 

        $query = School::where($field, $value);
        if ($excludeId) { 
            $query->where('id', '!=', $excludeId); 
        }
        
        return response()->json(['exists' => $query->exists()]);
    }

    public function audit()
    {
        $schools = School::all();
        $flaggedSchools = [];

        foreach ($schools as $school) {
            $issues = [];

            if ($school->no_of_teachers > 0 && ($school->no_of_enrollees / $school->no_of_teachers) > 45) {
                $issues[] = "Critical Teacher-Learner Ratio";
            }
            if ($school->no_of_classrooms > 0 && ($school->no_of_enrollees / $school->no_of_classrooms) > 45) {
                $issues[] = "Severe Classroom Overcrowding";
            }

            $hazards = is_array($school->hazard_type) ? $school->hazard_type : json_decode($school->hazard_type, true) ?? [];
            $hasPhysicalHazard = !empty($hazards) && !in_array('None', $hazards);

            if (!empty($issues) || $hasPhysicalHazard) {
                $flaggedSchools[] = [
                    'school' => $school,
                    'issues' => $issues
                ];
            }
        }

        return view('admin.audit', compact('flaggedSchools'));
    }

    private function processHazards($defaultHazards, $customHazards)
    {
        $allHazards = array_merge($defaultHazards ?? [], $customHazards ?? []);

        $cleanHazards = array_map(function($value) {
            $cleaned = ucwords(strtolower(trim($value)));
            $cleaned = str_ireplace([' Prone', ' Risk', ' Hazard'], '', $cleaned);
            return trim($cleaned);
        }, $allHazards);

        return array_values(array_unique(array_filter($cleanHazards)));
    }

    public function getApiData()
    {
        $schools = School::all();
        return response()->json([
            'status' => 'success',
            'data' => $schools
        ]);
    }
}