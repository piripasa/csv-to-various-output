<?php

namespace App\Jobs;

use App\Services\OutputService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessFileData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $outputType;
    protected $fileVersioning;
    protected $dnsValidation;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($outputType, $fileVersioning, $dnsValidation, $options)
    {
        $this->outputType = $outputType;
        $this->fileVersioning = $fileVersioning;
        $this->dnsValidation = $dnsValidation;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OutputService $outputService)
    {
        Log::info(json_encode($this->outputType));

        $outputService->processData($outputService->prepareData($this->dnsValidation), $this->outputType, $this->fileVersioning, $this->options);


    }
}
