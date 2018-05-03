<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 3:16 AM
 */

namespace App\Repositories;


class JsonOutput extends Output implements OutputInterface
{
    public function saveData($fileName, $data)
    {
        $this->writeToFile($fileName . '.json', json_encode($data));
    }
}