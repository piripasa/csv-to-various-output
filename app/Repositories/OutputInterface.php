<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 3:14 AM
 */

namespace App\Repositories;


interface OutputInterface
{
    public function saveData($fileName, $data);
}