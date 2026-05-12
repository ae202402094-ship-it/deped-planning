<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use Illuminate\Http\Request;

class GradeLevelController extends Controller
{
    public function index(Request $request) 
    {
        $query = GradeLevel::query();

        if ($request->filled('search')) {
            $query->where('level_name', 'like', '%' . $request->search . '%');
        }

        $grades = $query->get()->sortBy(function($grade) {
            $name = strtolower($grade->level_name);
            if (str_contains($name, 'kinder')) return 0;
            $number = (int) filter_var($name, FILTER_SANITIZE_NUMBER_INT);
            return $number > 0 ? $number : 99;
        });

        return view('admin_student_population', compact('grades'));
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'level_name'    => 'required|string',
            'section_count' => 'required|integer|min:1',
            'male_count'    => 'required|integer|min:0',
            'female_count'  => 'required|integer|min:0',
        ]);

        GradeLevel::create($validated);
        return back()->with('success', 'Grade Level added!');
    }

    // --- NEW UPDATE METHOD ---
    public function update(Request $request, GradeLevel $grade) 
    {
        $validated = $request->validate([
            'level_name'    => 'required|string',
            'section_count' => 'required|integer|min:1',
            'male_count'    => 'required|integer|min:0',
            'female_count'  => 'required|integer|min:0',
        ]);

        $grade->update($validated);
        return back()->with('success', 'Record updated successfully!');
    }

    public function destroy(GradeLevel $grade) 
    {
        $grade->delete();
        return back()->with('success', 'Record removed.');
    }
}