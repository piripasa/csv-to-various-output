<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 2:01 PM
 */

namespace App\Helpers;


class CsvValidator
{
    private $csvData, $validData, $inValidData, $rules, $headingRow, $errors, $headingKeys = [];

    public function make($csvPath, $rules, $encoding = 'UTF-8')
    {
        $this->csvData = [];
        $this->validData = [];
        $this->setRules($rules);
        $ruleKeys = $this->headingKeys;
        $excel = \App::make('excel');
        $excel->load($csvPath, function ($reader) use ($ruleKeys) {
            $reader->noHeading();
            if (env('TAKE_CSV_ROWS', 'all') == 'all') {
                $csvData = $reader->toArray();
            } else {
                $csvData = $reader->takeRows(env('TAKE_CSV_ROWS', 100))->toArray();
            }

            if ($this->isNoHeadings()) {
                $this->headingRow = $csvData[0];
                $newRules = [];
                //print_r($this->headingKeys);
                foreach ($this->headingKeys as $headingKey) {
                    $keyIndex = array_search($headingKey, $this->headingRow);
                    if ($keyIndex > -1) {
                        $newRules[$keyIndex] = $this->rules[$headingKey];
                    } else {
                        throw new \Exception('"' . $headingKey . '" not found.');
                    }
                }
                //$this->setRules($newRules);
                //dd($this->headingKeys);
                unset($csvData[0]);
                $csvData = array_values($csvData);
            }
            if (empty($csvData)) {
                throw new \Exception('No data found.');
            }
            $newCsvData = [];

            foreach ($csvData as $rowIndex => $csvValues) {
                $data = [];
                foreach ($ruleKeys as $ruleKeyIndex => $value) {
                    $data[$value] = $csvValues[$ruleKeyIndex];
                }
                //dd($data);
                $newCsvData[$rowIndex] = $data;
            }
            $this->csvData = $newCsvData;
        }, $encoding);
        return $this;
    }

    public function fails()
    {
        $errors = [];
        foreach ($this->csvData as $rowIndex => $csvValues) {
            $validator = \Validator::make($csvValues, $this->rules);
            if (!empty($this->headingRow)) {
                $validator->setAttributeNames($this->headingRow);
            }
            if ($validator->fails()) {
                $errors[$rowIndex] = $validator->messages()->toArray();
                $csvValues['errors'] = $errors[$rowIndex];
                $csvValues['position'] = $rowIndex + 1;
                $this->inValidData[$rowIndex] = $csvValues;
            } else {
                $this->validData[$rowIndex] = $csvValues;
            }
        }
        $this->errors = $errors;
        return (!empty($this->errors));
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getData()
    {
        return $this->csvData;
    }

    public function getValidData()
    {
        return $this->validData;
    }

    public function getInValidData()
    {
        return $this->inValidData;
    }

    public function setAttributeNames($attributeNames)
    {
        $this->headingRow = $attributeNames;
    }

    private function setRules($rules)
    {
        $this->rules = $rules;
        $this->headingKeys = array_keys($rules);
    }

    private function isNoHeadings()
    {
        foreach ($this->headingKeys as $headingKey) {
            if (!is_int($headingKey)) {
                return true;
            }
        }
        return false;
    }
}
