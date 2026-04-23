<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchoolImportController extends Controller
{
    public function clearAllSchools()
    {
        try {
            School::query()->delete(); 
            return redirect()->route('admin.schools')->with('success', 'School registry has been soft-deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'System Error: Wipe protocol failed.');
        }
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
        $nameMismatchCount = 0; 
        $seenIds = []; 

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (!empty($row[0])) {
                $currentId = (string)$row[0];
                $currentName = (string)($row[1] ?? 'N/A');
                
                $existingSchoolById = School::withTrashed()->where('school_id', $currentId)->first();
                $existingSchoolByName = School::withTrashed()->where('name', $currentName)
                        ->where('school_id', '!=', $currentId)
                        ->first();

                if (isset($seenIds[$currentId])) {
                    $status = 'conflict';
                    $conflictCount++;
                } elseif ($existingSchoolByName) {
                    $status = 'name_mismatch'; 
                    $nameMismatchCount++;
                } else {
                    $status = $existingSchoolById ? 'update' : 'new';
                    $existingSchoolById ? $updateCount++ : $newCount++;
                }

                $seenIds[$currentId] = true;

                $formattedData[] = [
                    'school_id'          => $currentId,
                    'name'               => $currentName,
                    
                    // Stripped commas before casting to int
                    'no_of_teachers'     => (int)str_replace(',', '', $row[2] ?? 0),
                    'no_of_enrollees'    => (int)str_replace(',', '', $row[3] ?? 0),
                    'no_of_classrooms'   => (int)str_replace(',', '', $row[4] ?? 0),
                    'no_of_toilets'      => (int)str_replace(',', '', $row[5] ?? 0),
                    'no_of_chairs'       => (int)str_replace(',', '', $row[6] ?? 0),
                    
                    // Stripped commas before casting to float (just in case)
                    'latitude'           => !empty($row[7]) ? (float)str_replace(',', '', $row[7]) : 6.9214,
                    'longitude'          => !empty($row[8]) ? (float)str_replace(',', '', $row[8]) : 122.0739,
                    
                    'with_electricity'   => (string)($row[9] ?? 'None'),
                    'with_potable_water' => filter_var($row[10] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'with_internet'      => filter_var($row[11] ?? false, FILTER_VALIDATE_BOOLEAN),
                    
                    // Stripped commas before casting to int
                    'classroom_shortage' => (int)str_replace(',', '', $row[12] ?? 0),
                    'chair_shortage'     => (int)str_replace(',', '', $row[13] ?? 0),
                    'toilet_shortage'    => (int)str_replace(',', '', $row[14] ?? 0),
                    
                    'hazards'            => (string)($row[15] ?? 'None'),
                    
                    'status'             => $status,
                    'exists_in_db'       => (bool)$existingSchoolById,
                    'mismatch_id'        => $existingSchoolByName ? $existingSchoolByName->school_id : null,
                    'old_values'         => $existingSchoolById ? [
                        'no_of_teachers'  => $existingSchoolById->no_of_teachers,
                        'no_of_enrollees' => $existingSchoolById->no_of_enrollees,
                        'name'            => $existingSchoolById->name,
                    ] : null,
                ];
            }
        }
        fclose($handle);

        session(['pending_import' => $formattedData]);
        
        return view('admin.preview_import', compact(
            'formattedData', 'newCount', 'updateCount', 'conflictCount', 'nameMismatchCount'
        ));
    }

    public function confirmImport(Request $request)
    {
        $data = session('pending_import');
        if (!$data) {
            return redirect()->route('admin.schools')->with('error', 'No pending data found.');
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $row) {
                $schoolId = (string)$row['school_id'];

                $archivedSchool = School::onlyTrashed()->where('school_id', $schoolId)->first();
                if ($archivedSchool) {
                    $archivedSchool->forceDelete();
                }

                School::updateOrCreate(
                    ['school_id' => $schoolId], 
                    [
                        'name'               => (string)$row['name'],
                        'no_of_teachers'     => (int)$row['no_of_teachers'],
                        'no_of_enrollees'    => (int)$row['no_of_enrollees'],
                        'no_of_classrooms'   => (int)$row['no_of_classrooms'],
                        'no_of_toilets'      => (int)$row['no_of_toilets'],
                        'no_of_chairs'       => (int)$row['no_of_chairs'],
                        'latitude'           => (float)$row['latitude'],
                        'longitude'          => (float)$row['longitude'],
                        'with_electricity'   => (string)$row['with_electricity'], 
                        'with_potable_water' => (bool)$row['with_potable_water'], 
                        'with_internet'      => (bool)$row['with_internet'], 
                        'classroom_shortage' => (int)$row['classroom_shortage'], 
                        'chair_shortage'     => (int)$row['chair_shortage'], 
                        'toilet_shortage'    => (int)$row['toilet_shortage'], 
                        'hazards'            => (string)$row['hazards'], 
                    ]
                );
            }
        });

        session()->forget('pending_import');
        return redirect()->route('admin.schools')->with('success', 'Registry synchronized.');
    }

    public function downloadSampleCSV(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            
            // Write Headers
            fputcsv($handle, [
                'school_id', 'name', 'no_of_teachers', 'no_of_enrollees', 
                'no_of_classrooms', 'no_of_toilets', 'no_of_chairs', 
                'latitude', 'longitude', 'with_electricity', 'with_potable_water', 
                'with_internet', 'classroom_shortage', 'chair_shortage', 'toilet_shortage', 'hazards'
            ]);
            
            // Write Sample Data 
            fputcsv($handle, [
                '123456', 'SAMPLE SCHOOL', '20', '500', '15', '8', '450', 
                '6.9214', '122.0739', 'Grid Connection', 'yes', 'no', '0', '50', '2', 'Flood Prone'
            ]);
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="deped_census_full_template.csv"',
        ]);
    }
}