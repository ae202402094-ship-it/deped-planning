<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CensusController extends Controller
{
    public function index(Request $request)
    {
        // Handle Delete
        if ($request->has('delete')) {
            DB::table('teacher_rankings')->where('id', $request->delete)->delete();
            return redirect()->back()->with('success', 'Rank deleted successfully!');
        }

        // Handle Add
        if ($request->has('add_rank')) {
            DB::table('teacher_rankings')->insert([
                'career_stage' => $request->new_stage,
                'position_title' => $request->new_title,
                'salary_grade' => (int)$request->new_sg,
                'teacher_count' => (int)$request->new_count,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return redirect()->back()->with('success', 'New rank added!');
        }

        // Handle Bulk Update
        if ($request->has('update_all')) {
            foreach ($request->counts as $id => $count) {
                DB::table('teacher_rankings')->where('id', $id)->update([
                    'teacher_count' => (int)$count,
                    'updated_at' => now()
                ]);
            }
            return redirect()->back()->with('success', 'Census updated!');
        }

        // Fetch Data
        // Fetch Data
$search = $request->query('search', '');
$filterStage = $request->query('filter_stage', '');

$rankings = DB::table('teacher_rankings')
    ->when($search, function($q) use ($search) {
        return $q->where('position_title', 'ILIKE', "%{$search}%");
    })
    ->when($filterStage, function($q) use ($filterStage) {
        return $q->where('career_stage', $filterStage);
    })
    ->orderBy('salary_grade', 'asc')
    ->get();

$totalTeachers = $rankings->sum('teacher_count');

        return view('index', compact('rankings', 'totalTeachers', 'search'));
    }
}