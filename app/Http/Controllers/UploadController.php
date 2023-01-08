<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Storage;

final class UploadController extends Controller
{
    public function lazyCollection(Request $request): JsonResponse
    {
        $this->validate($request, [
            'attachment' => 'required|mimes:csv'
        ]);

        try {
            Storage::disk('public')->putFileAs('uploads', $request->file('attachment'), 'big_file_csv_upload.csv');
        
            LazyCollection::make(function () {
                $handle = fopen(app()->basePath() . '/public/uploads/big_file_csv_upload.csv', 'r');
                
                while (($line = fgetcsv($handle, 4096)) !== false) {
                    // $data_string = implode(", ", $line);
                    // $row = explode(';', $data_string);
                    // yield $row;
                    // dd($line);
                    yield $line;
                }
        
                fclose($handle);
            })
            ->skip(1) // remove header
            ->chunk(1000) // chunk every 1000 / array
            ->each(function (LazyCollection $chunk) {
                $records = $chunk->map(function ($row) {
                    return [
                        "year" => $row[0],
                        "industry_aggregation" => $row[1],
                        "industry_code" => $row[2],
                        "industry_name" => $row[3],
                        "units" => $row[4],
                        "variable_code" => $row[5],
                        "variable_name" => $row[6],
                        "variable_category" => $row[7],
                        "value" => $row[8],
                    ];
                })->toArray();

                // dd($records);
                
                DB::table('enterprise')->insert($records);
            });

            return response()->json([
                'message' => 'Berhasil memproses csv', 
                'memory_usage' => (memory_get_peak_usage(true) / 1024 / 1024) . ' MiB'
            ], 200);
        } catch (\Exception $e) {
            // throw $e;
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

}