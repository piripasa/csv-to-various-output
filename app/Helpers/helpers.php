<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 16/2/18
 * Time: 6:01 PM
 */

function arrayToXml( $data, &$xmlData ) {
    foreach( $data as $key => $value ) {
        if( is_numeric($key) ){
            $key = 'hotel';
        }
        if( is_array($value) ) {
            $subNode = $xmlData->addChild(createSlug($key, '_'));
            arrayToXml($value, $subNode);
        } else {
            $xmlData->addChild(createSlug($key, '_'), htmlspecialchars("$value"));
        }
    }

    return $xmlData;
}

function createSlug($str, $delimiter = '-'){

    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;

}

function arrayToHtmlTable($data)
{
    $rows = [];
    //$rows[] = "<tr><th>" .implode('</th><th>', array_keys(current($data))) . "</th></tr>";
    $rows[] = "<tr><th>Name</th><th>Address</th><th>Stars</th><th>Contact</th><th>Phone</th><th>Uri</th></tr>";
    foreach ($data as $index => $row) {
        if (array_key_exists('name', $row)) {
            $rows[] = arrayToTableRow($row);
        }
        else {
            $rows[] = "<tr><td colspan='6' bgcolor='red'>" . $index . "</td></tr>";
            foreach ($row as $row2) {
                $rows[] = arrayToTableRow($row2);
            }
        }
    }

    return "<table class='trivago-table'>" . implode('', $rows) . "</table>";
}

function arrayToTableRow($row)
{
    $cells = [];
    foreach ($row as $key => $cell) {
        $cells[] = "<td>{$cell}</td>";
    }
    return "<tr>" . implode('', $cells) . "</tr>";

}

function arrayGroup($array, $key)
{
    $return = [];
    foreach($array as $index => $val) {
        $return[$val[$key]][] = $val;
    }

    return $return;
}

function arraySort($array, $sortBy, $sortOrder)
{
    $sorted = [];
    foreach ($array as $key => $row)
    {
        $sorted[$key] = $row[$sortBy];
    }

    array_multisort($sorted, ($sortOrder == 'asc') ? SORT_ASC : SORT_DESC, $array);

    return $array;
}

function arrayFilter($array, $filterBy, $filterValue)
{
    $return = array_filter($array, function ($row) use ($filterBy, $filterValue) {
        return ($row[$filterBy] == $filterValue);
    });

    return array_values($return);
}
