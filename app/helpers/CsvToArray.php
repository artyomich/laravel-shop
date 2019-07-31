<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace helpers;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class CsvToArray
{
    /**
     * @param $filename
     * @line int с какой строки считывать
     * @return array|bool
     */
    public static function toArray($filename, $delimiter = ',', $line = 0)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;

        ini_set('auto_detect_line_endings', true);

        $header = null;
        $data = array();
        $i = 0;
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if ($i === $line) {
                    $header = $row;
                } elseif ($header) {
                    $data[] = array_combine($header, $row);
                }
                $i++;
            }
            fclose($handle);
        }

        ini_set('auto_detect_line_endings', false);

        return $data;
    }

    /**
     * @param $array
     */
    public static function toCSV(array $array, $delimiter = ',')
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    /**
     * Преобразует csv текст в массив.
     * @param string $text
     * @param string $delimiter
     * @return array
     */
    public static function textToArray($text, $delimiter = ',')
    {
        $lines = explode("\n", $text);
        $header = [];
        $data = [];


        foreach ($lines as $k => $line) {
            if ($k == 0) {
                $header = str_getcsv($line, $delimiter);
            } else {
                $data[] = array_combine($header, str_getcsv($line, $delimiter));
            }
        }

        return $data;
    }
}