<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 5:23 PM
 */

namespace App\Repositories;

class XmlOutput  extends Output implements OutputInterface
{
    public function saveData($fileName, $data)
    {
        $data['total_hotel'] = count($data);
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
        $this->writeToFile($fileName . '.xml', arrayToXml($data, $xml)->asXML());
    }
}