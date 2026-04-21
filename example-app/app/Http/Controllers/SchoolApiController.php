<?
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolApiController extends Controller
{
    public function getSchoolSummary()
    {
        // Fetch only the specific columns the other team needs to see
        // You don't want to expose sensitive admin data!
        $schools = School::select(
            'school_id', 
            'name', 
            'latitude', 
            'longitude', 
            'hazard_type',
            'with_electricity'
        )->get();

        // Return a JSON response with a success status
        return response()->json([
            'status' => 'success',
            'count' => $schools->count(),
            'data' => $schools
        ], 200);
    }
}