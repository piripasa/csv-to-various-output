<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 17/2/18
 * Time: 12:11 AM
 */

namespace App\Repositories;

use Symfony\Component\Yaml\Yaml;

class YamlOutput extends Output implements OutputInterface
{
    public function saveData($fileName, $data)
    {
        $this->writeToFile($fileName . '.yml', Yaml::dump($data));
    }
}