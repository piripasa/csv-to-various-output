<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateFileRequest;
use App\Jobs\ProcessFileData;
use App\Services\OutputService;
use Illuminate\Support\Facades\Log;

class OutputController extends Controller
{
    public function generate(GenerateFileRequest $request, OutputService $outputService)
    {
        try {
            $outputType = $request->get('output');
            $fileVersioning = $request->get('versioning', false);
            $dnsValidation = $request->get('dns_validation', false);
            $sort = $request->get('sort', false);
            $sortOrder = $request->get('sort_order', 'asc');
            $group = $request->get('group', false);
            $filter = $request->get('filter', false);
            $filterValue = $request->get('filter_value');
            //Log::info(json_encode($request->all()));
            $options = [];

            if ($sort) {
                $options['sort'] = $sort;
                $options['sort_order'] = $sortOrder;
            }

            if ($group) {
                $options['group'] = $group;
            }

            if ($filter) {
                $options['filter'] = $filter;
                $options['filter_value'] = $filterValue;
            }

            ProcessFileData::dispatch($outputType, $fileVersioning, $dnsValidation, $options);
            //$outputService->processData($outputService->prepareData(false), $outputType, $fileVersioning, $options);

            return response()->json([
                'message' => 'Data saving as ' . implode(',', $outputType) . ' format is under processing.
                You will get your data file shortly in trivago_files folder at project root. Please run "php artisan queue:work redis"'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 417);
        }
    }
}
