<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 4:38 PM
 */

namespace App\Services;


use App\Helpers\CsvValidator;
use App\Repositories\OutputFactory;
use App\Repositories\OutputRepository;
use App\Rules\NonAscii;

class OutputService
{
    public function prepareData($dnsValidation)
    {
        $rules = [
            'name' => ['required', new NonAscii()],
            'address' => 'required|string',
            'stars' => 'required|integer|between:1,5',
            'contact' => 'required|string',
            'phone' => 'required',
            'uri' => $dnsValidation ? 'url|active_url' : 'url',
        ];

        $csvValidator = new CsvValidator();

        return $csvValidator->make(config('filesystems.disks.trivago.root') . '/hotels.csv', $rules);

    }

    public function processData($csvValidator, $outputType, $fileVersioning, $options)
    {
        $outputRepository = new OutputRepository();

        if($csvValidator->fails()) {

            $errors = $csvValidator->getErrors();
            $log = [];
            foreach ($errors as $row_index => $error) {
                foreach ($error as $col_index => $messages) {
                    $msg = implode(',', $messages);
                    $log[] = "Row {$row_index}, Col {$col_index} : {$msg}";
                }
            }

            $outputRepository->setFileVersioning($fileVersioning)
                ->setFileName('validation-error')
                ->setData($log)
                ->save(OutputFactory::processOutput('error'));

        }

        $validData = array_values($csvValidator->getValidData());

        foreach ($outputType as $output) {
            $outputRepository->setFileVersioning($fileVersioning)
                ->setFileName('hotels')
                ->setOptions($options)
                ->setData($validData)
                ->save(OutputFactory::processOutput($output));
        }

        return;
    }
}