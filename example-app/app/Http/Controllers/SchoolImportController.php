<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchoolImportController extends Controller
{
    /**
     * Process CSV and redirect to GET preview
     */
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
        $seenIdsInCsv = []; 

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (empty($row[0])) continue;

            $currentId = (string)$row[0];
            $currentName = (string)($row[1] ?? 'N/A');
            
            $existing = School::withTrashed()->where('school_id', $currentId)->first();
            
            $status = 'new';
            $changes = [];

            if (isset($seenIdsInCsv[$currentId])) {
                $status = 'conflict';
                $conflictCount++;
            } elseif ($existing) {
                $status = 'update';
                $updateCount++;

                $fields = [
                    'name' => $currentName,
                    'no_of_teachers' => (int)str_replace(',', '', $row[2] ?? 0),
                    'no_of_enrollees' => (int)str_replace(',', '', $row[3] ?? 0),
                    'no_of_classrooms' => (int)str_replace(',', '', $row[4] ?? 0),
                    'no_of_chairs' => (int)str_replace(',', '', $row[6] ?? 0),
                    'with_electricity' => (string)($row[9] ?? 'None'),
                    'with_potable_water' => filter_var($row[10] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'with_internet' => filter_var($row[11] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'classroom_shortage' => (int)str_replace(',', '', $row[12] ?? 0),
                    'chair_shortage' => (int)str_replace(',', '', $row[13] ?? 0),
                    'toilet_shortage' => (int)str_replace(',', '', $row[14] ?? 0),
                ];

                foreach ($fields as $field => $val) {
                    if ($existing->$field != $val) {
                        $changes[$field] = true;
                    }
                }
            } else {
                $newCount++;
            }

            $seenIdsInCsv[$currentId] = true;
            $formattedData[] = [
                'school_id' => $currentId,
                'name' => $currentName,
                'no_of_teachers' => (int)str_replace(',', '', $row[2] ?? 0),
                'no_of_enrollees' => (int)str_replace(',', '', $row[3] ?? 0),
                'no_of_classrooms' => (int)str_replace(',', '', $row[4] ?? 0),
                'no_of_toilets' => (int)str_replace(',', '', $row[5] ?? 0),
                'no_of_chairs' => (int)str_replace(',', '', $row[6] ?? 0),
                'latitude' => !empty($row[7]) ? (float)str_replace(',', '', $row[7]) : 6.9214,
                'longitude' => !empty($row[8]) ? (float)str_replace(',', '', $row[8]) : 122.0739,
                'with_electricity' => (string)($row[9] ?? 'None'),
                'with_potable_water' => filter_var($row[10] ?? false, FILTER_VALIDATE_BOOLEAN),
                'with_internet' => filter_var($row[11] ?? false, FILTER_VALIDATE_BOOLEAN),
                'classroom_shortage' => (int)str_replace(',', '', $row[12] ?? 0),
                'chair_shortage' => (int)str_replace(',', '', $row[13] ?? 0),
                'toilet_shortage' => (int)str_replace(',', '', $row[14] ?? 0),
                'hazards' => (string)($row[15] ?? 'None'),
                'status' => $status,
                'changes' => $changes,
            ];
        }
        fclose($handle);

        session(['pending_import' => $formattedData]);
        session(['import_counts' => [
            'new' => $newCount,
            'update' => $updateCount,
            'conflict' => $conflictCount,
            'mismatch' => $nameMismatchCount
        ]]);

        return redirect()->route('schools.import.preview');
    }

    public function showPreview(Request $request)
    {
        $formattedData = session('pending_import');
        $counts = session('import_counts');

        if (!$formattedData) {
            return redirect()->route('admin.schools')->with('error', 'No import data found.');
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = array_slice($formattedData, ($currentPage - 1) * $perPage, $perPage);
        $paginatedData = new LengthAwarePaginator($currentItems, count($formattedData), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('admin.preview_import', [
            'formattedData' => $paginatedData,
            'newCount' => $counts['new'] ?? 0,
            'updateCount' => $counts['update'] ?? 0,
            'conflictCount' => $counts['conflict'] ?? 0,
            'nameMismatchCount' => $counts['mismatch'] ?? 0,
        ]);
    }

    public function confirmImport(Request $request)
    {
        $data = session('pending_import');
        if (!$data) return redirect()->route('admin.schools')->with('error', 'No pending data.');

        DB::transaction(function () use ($data) {
            foreach ($data as $row) {
                if ($row['status'] === 'conflict') continue; 

                School::withTrashed()->updateOrCreate(['school_id' => $row['school_id']], [
                    'name' => $row['name'],
                    'no_of_teachers' => $row['no_of_teachers'],
                    'no_of_enrollees' => $row['no_of_enrollees'],
                    'no_of_classrooms' => $row['no_of_classrooms'],
                    'no_of_toilets' => $row['no_of_toilets'],
                    'no_of_chairs' => $row['no_of_chairs'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'with_electricity' => $row['with_electricity'], 
                    'with_potable_water' => $row['with_potable_water'], 
                    'with_internet' => $row['with_internet'], 
                    'classroom_shortage' => $row['classroom_shortage'], 
                    'chair_shortage' => $row['chair_shortage'], 
                    'toilet_shortage' => $row['toilet_shortage'], 
                    'hazards' => $row['hazards'], 
                ]);
            }
        });

        session()->forget(['pending_import', 'import_counts']);
        return redirect()->route('admin.schools')->with('success', 'Sync complete. Duplicates ignored.');
    }

    public function downloadSampleCSV(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'school_id', 'name', 'no_of_teachers', 'no_of_enrollees', 
                'no_of_classrooms', 'no_of_toilets', 'no_of_chairs', 
                'latitude', 'longitude', 'with_electricity', 'with_potable_water', 
                'with_internet', 'classroom_shortage', 'chair_shortage', 'toilet_shortage', 'hazards'
            ]);
            fputcsv($handle, [
                '123456', 'SAMPLE SCHOOL', '20', '500', '15', '8', '450', 
                '6.9214', '122.0739', 'Grid Connection', 'yes', 'no', '0', '50', '2', 'None'
            ]);
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="deped_census_template.csv"',
        ]);
    }
}