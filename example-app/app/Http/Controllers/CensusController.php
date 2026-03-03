<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherRanking;
use App\Models\School;

class CensusController extends Controller
{
    public function listSchools()
    {
        $schools = School::all(); 
        return view('schools_list', compact('schools'));
    }

    // PUBLIC: Show teacher rankings (filtered by school if requested)
    public function showPublic(Request $request)
    {
        $schoolId = $request->query('school_id');
        $search = $request->query('search');

        $rankings = TeacherRanking::with('school')
            ->when($schoolId, function($q) use ($schoolId) {
                return $q->where('school_id', $schoolId);
            })
            ->when($search, function($q) use ($search) {
                return $q->where('position_title', 'like', "%{$search}%");
            })
            ->orderBy('salary_grade', 'asc')
            ->get();

        // Get school name for the header title
        $selectedSchool = $schoolId ? School::find($schoolId) : null;

        return view('user_view', compact('rankings', 'selectedSchool'));
    }

    public function adminDashboard()
    {
        return view('admin.dashboard', [
            'schoolCount' => School::count(),
            'teacherCount' => TeacherRanking::sum('teacher_count')
        ]);
    }

    public function manageSchools() {
        return view('admin.schools', ['schools' => School::all()]);
    }

    public function storeSchool(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'location' => 'nullable']);
        School::create($request->only('name', 'location'));
        return redirect()->back()->with('success', 'School added successfully!');
    }

    public function manageTeachers(Request $request) 
    {
        // 1. Handle Actions (POST)
        if ($request->isMethod('post')) {
            if ($request->has('add_rank')) {
                TeacherRanking::create([
                    'school_id'      => $request->school_id,
                    'career_stage'   => $request->new_stage,
                    'position_title' => $request->new_title,
                    'salary_grade'   => (int)$request->new_sg,
                    'teacher_count'  => (int)$request->new_count,
                ]);
                return redirect()->back()->with('success', 'Rank added!');
            }

            if ($request->has('delete')) {
                TeacherRanking::destroy($request->delete);
                return redirect()->back()->with('success', 'Rank deleted!');
            }

            if ($request->has('update_all')) {
                foreach ($request->counts as $id => $count) {
                    TeacherRanking::where('id', $id)->update(['teacher_count' => (int)$count]);
                }
                return redirect()->back()->with('success', 'Census updated!');
            }
        }

        // 2. Fetch Data (GET)
       $schools = School::all();
    $selectedSchoolId = $request->query('school_id'); // Get the school filter from the URL

    $rankings = TeacherRanking::with('school')
        ->when($selectedSchoolId, function($q) use ($selectedSchoolId) {
            return $q->where('school_id', $selectedSchoolId);
        })
        ->orderBy('salary_grade', 'asc')
        ->get();

    $totalTeachers = $rankings->sum('teacher_count');

    return view('admin.teachers', compact('rankings', 'schools', 'totalTeachers', 'selectedSchoolId'));
}
}