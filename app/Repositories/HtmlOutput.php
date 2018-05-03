<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 17/2/18
 * Time: 12:49 AM
 */

namespace App\Repositories;


class HtmlOutput extends Output implements OutputInterface
{
    public function saveData($fileName, $data)
    {
        $this->writeToFile($fileName . '.html', arrayToHtmlTable($data));
    }

}