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

            // Mapping all row data including the 3 new columns
            $rowData = [
                'school_id' => $currentId,
                'name' => $currentName,
                'sector' => (string)($row[2] ?? 'Public'),
                'school_level' => (string)($row[3] ?? 'Primary'),
                'district' => (string)($row[4] ?? 'Unknown'),
                'no_of_teachers' => (int)str_replace(',', '', $row[5] ?? 0),
                'no_of_enrollees' => (int)str_replace(',', '', $row[6] ?? 0),
                'no_of_classrooms' => (int)str_replace(',', '', $row[7] ?? 0),
                'no_of_chairs' => (int)str_replace(',', '', $row[8] ?? 0),
                'no_of_toilets' => (int)str_replace(',', '', $row[9] ?? 0),
                'latitude' => !empty($row[10]) ? (float)str_replace(',', '', $row[10]) : 6.9214,
                'longitude' => !empty($row[11]) ? (float)str_replace(',', '', $row[11]) : 122.0739,
                'with_electricity' => (string)($row[12] ?? 'None'),
                'with_potable_water' => filter_var($row[13] ?? false, FILTER_VALIDATE_BOOLEAN),
                'with_internet' => filter_var($row[14] ?? false, FILTER_VALIDATE_BOOLEAN),
                'classroom_shortage' => (int)str_replace(',', '', $row[15] ?? 0),
                'chair_shortage' => (int)str_replace(',', '', $row[16] ?? 0),
                'toilet_shortage' => (int)str_replace(',', '', $row[17] ?? 0),
                'hazards' => (string)($row[18] ?? 'None'),
            ];

            if (isset($seenIdsInCsv[$currentId])) {
                $status = 'conflict';
                $conflictCount++;
            } elseif ($existing) {
                $status = 'update';
                $updateCount++;

                foreach ($rowData as $field => $val) {
                    if ($field === 'school_id' || $field === 'status' || $field === 'changes') continue;
                    
                    if ($existing->$field != $val) {
                        $changes[$field] = true;
                    }
                }
            } else {
                $newCount++;
            }

            $seenIdsInCsv[$currentId] = true;
            $rowData['status'] = $status;
            $rowData['changes'] = $changes;
            $formattedData[] = $rowData;
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

                $school = School::withTrashed()->firstOrNew(['school_id' => $row['school_id']]);
                
                // Use forceFill to bypass $fillable restrictions during bulk import
                $school->forceFill([
                    'name' => $row['name'],
                    'sector' => $row['sector'],
                    'school_level' => $row['school_level'],
                    'district' => $row['district'],
                    'no_of_teachers' => $row['no_of_teachers'],
                    'no_of_enrollees' => $row['no_of_enrollees'],
                    'no_of_classrooms' => $row['no_of_classrooms'],
                    'no_of_chairs' => $row['no_of_chairs'],
                    'no_of_toilets' => $row['no_of_toilets'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'with_electricity' => $row['with_electricity'], 
                    'with_potable_water' => $row['with_potable_water'], 
                    'with_internet' => $row['with_internet'], 
                    'classroom_shortage' => $row['classroom_shortage'], 
                    'chair_shortage' => $row['chair_shortage'], 
                    'toilet_shortage' => $row['toilet_shortage'], 
                    'hazard_type' => [$row['hazards']], // Cast back to array
                ])->save();

                if ($school->trashed()) {
                    $school->restore();
                }
            }
        });

        session()->forget(['pending_import', 'import_counts']);
        return redirect()->route('admin.schools')->with('success', 'Sync complete. Registry Updated.');
    }

    public function downloadSampleCSV(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            
            // Updated Headers to include Sector, Level, and District
            fputcsv($handle, [
                'school_id', 'name', 'sector', 'school_level', 'district', 'no_of_teachers', 'no_of_enrollees', 
                'no_of_classrooms', 'no_of_chairs', 'no_of_toilets', 
                'latitude', 'longitude', 'with_electricity', 'with_potable_water', 
                'with_internet', 'classroom_shortage', 'chair_shortage', 'toilet_shortage', 'hazards'
            ]);
            
            // Sample Data 1
            fputcsv($handle, [
                '124019', 'Tetuan Central School', 'Public', 'Primary', 'Tetuan', '105', '3200', '95', '3100', '40', 
                '6.9214', '122.0739', 'Grid Connection', '1', '1', '0', '100', '2', 'Flood Prone'
            ]);

            // Sample Data 2
            fputcsv($handle, [
                '800123', 'Claret School of Zamboanga', 'Private', 'Secondary', 'Central', '90', '2100', '85', '2200', '50', 
                '6.9100', '122.0760', 'Hybrid', '1', '1', '0', '0', '0', 'None'
            ]);

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="deped_census_template.csv"',
        ]);
    }
}