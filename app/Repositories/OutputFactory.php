<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 3:09 AM
 */

namespace App\Repositories;


class OutputFactory
{
    /** return OutputInterface */
    public static function processOutput($type)
    {
        switch($type)
        {
            case 'json':
                return new JsonOutput();
                break;

            case 'xml':
                return new XmlOutput();
                break;

            case 'sqlite':
                return new SqliteOutput();
                break;

            case 'yaml':
                return new YamlOutput();
                break;

            case 'html':
                return new HtmlOutput();
                break;

            case 'error':
                return new ErrorOutput();
                break;
        }
    }
}