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
        })->orderBy('name', 'asc')->paginate(50); 

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

    public function editSchool($id)
    {
        $school = School::findOrFail($id);
        return view('admin.edit_school', compact('school'));
    }

    public function updateSchool(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $oldData = $school->only([
            'name', 'school_id', 'no_of_teachers', 
            'no_of_enrollees', 'no_of_classrooms', 'no_of_toilets',
            'latitude', 'longitude' 
        ]);

        $validated = $request->validate([
            'school_id' => 'required|string',
            'name' => 'required|string',
            'no_of_teachers' => 'required|integer',
            'no_of_enrollees' => 'required|integer',
            'no_of_classrooms' => 'required|integer',
            'no_of_toilets' => 'required|integer',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $school->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated School Profile',
            'target_name' => $school->name,
            'changes' => [
                'before' => $oldData,
                'after' => $request->only([
                    'name', 'school_id', 'no_of_teachers', 
                    'no_of_enrollees', 'no_of_classrooms', 'no_of_toilets',
                    'latitude', 'longitude'
                ])
            ]
        ]);

        return redirect()->route('schools.edit', $id)->with('success', 'Registry synchronized.');
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

    public function manageTeachers(Request $request) 
    {
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

        $schools = School::all();
        $totalTeachers = $schools->sum('no_of_teachers');

        return view('admin.teachers', compact('schools', 'totalTeachers'));
    }
}