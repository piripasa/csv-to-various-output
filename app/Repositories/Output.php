<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 3:17 AM
 */

namespace App\Repositories;


use Illuminate\Support\Facades\Storage;

abstract class Output
{
    public function writeToFile($fileName, $data)
    {
        Storage::disk('trivago')->put($fileName, $data);
    }
}