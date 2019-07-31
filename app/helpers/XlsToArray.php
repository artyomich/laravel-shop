<?php

namespace helpers;


class XlsToArray
{
    /**
     * @param $filename
     *
     * @return array|bool
     */
    public static function toArray($filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }
        $filename_csv = '/tmp/' . \Excel::load($filename)->store('csv', '/tmp')->getFileName($filename) . '.csv';
        $data = CsvToArray::toArray($filename_csv, ',', 1);
        \File::delete($filename_csv);
        return $data;
    }
}