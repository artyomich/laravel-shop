<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace helpers;

class StringHelper
{
    /**
     * Переводит кириллицу в транслит.
     * @param $string
     * @return string
     */
    public static function translit($string)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    public static function translitLow($string)
    {
        return strtolower(self::translit($string));
    }

    public static function hasTube($completeness)
    {
        $completeness = str_replace('  ', ' ', $completeness);
        if (strpos($completeness, 'с кам') !== false OR strpos($completeness, 'камера') !== false)
            return 'с камерой';
        if (strpos($completeness, 'без кам') !== false OR strpos($completeness, 'б/к') !== false)
            return 'без камеры';

    }

    public static function hasRimTape($completeness)
    {
        if (strpos($completeness, 'о/л') !== false OR strpos($completeness, 'лента') !== false)
            return 'с ободной лентой';
    }

    public static function hasTubeRimeTape($completeness)
    {
        $hasTube = self::hasTube($completeness);
        $hasRimTape = self::hasRimTape($completeness);
        return $hasTube . ($hasTube && $hasRimTape ? ', ' : '') . $hasRimTape;
    }
}