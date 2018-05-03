<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 17/2/18
 * Time: 10:04 PM
 */

namespace App\Repositories;

class ErrorOutput extends Output implements OutputInterface
{
    public function saveData($fileName, $data)
    {
        $this->writeToFile($fileName . '.log', implode("\n", $data));
    }
}