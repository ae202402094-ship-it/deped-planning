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
            School::truncate(); 
            return redirect()->route('admin.schools')->with('success', 'DEBUG: School registry has been completely wiped.');
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
                
                $existingSchoolById = School::where('school_id', $currentId)->first();
                $existingSchoolByName = School::where('name', $currentName)
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
        });

        session()->forget('pending_import');
        return redirect()->route('admin.schools')->with('success', 'Registry synchronized.');
    }

    public function downloadSampleCSV(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'school_id', 'name', 'no_of_teachers', 'no_of_enrollees', 
                'no_of_classrooms', 'no_of_toilets', 'latitude', 'longitude'
            ]);
            fputcsv($handle, ['123456', 'SAMPLE SCHOOL', '20', '500', '15', '8', '6.9214', '122.0739']);
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="deped_census_map_template.csv"',
        ]);
    }
}