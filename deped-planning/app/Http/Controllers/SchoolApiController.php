<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolApiController extends Controller
{
    public function getSchoolSummary(Request $request)
    {
        // Start a base query selecting the needed columns
        $query = School::select(
            'school_id', 
            'name', 
            'school_level',
            'district',
            'latitude', 
            'longitude', 
            'hazard_type',
            'with_electricity'
        );

        // 1. Filter by Level if requested (e.g., ?level=Primary)
        if ($request->has('level')) {
            $level = $request->input('level');
            if ($level === 'Primary') {
                $query->primary();
            } elseif ($level === 'Secondary') {
                $query->secondary();
            }
        }

        // 2. Filter by District if requested (e.g., ?district=Baliwasan)
        if ($request->has('district')) {
            $query->inDistrict($request->input('district'));
        }

        // Execute the query
        $schools = $query->get();

        return response()->json([
            'status' => 'success',
            'count' => $schools->count(),
            'data' => $schools
        ], 200);
    }
}