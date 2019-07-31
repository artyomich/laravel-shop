<?php

namespace helpers;

class ValidatorHelper
{
    public static function inn($attribute, $value, $parameters)
    {
        $inn = $value;
        if (preg_match('/\D/', $inn)) return false;
        $inn = (string)$inn;
        $len = strlen($inn);
        if ($len === 10) {
            return $inn[9] === (string)(((
                        2 * $inn[0] + 4 * $inn[1] + 10 * $inn[2] +
                        3 * $inn[3] + 5 * $inn[4] + 9 * $inn[5] +
                        4 * $inn[6] + 6 * $inn[7] + 8 * $inn[8]
                    ) % 11) % 10);
        } elseif ($len === 12) {
            $num10 = (string)(((
                        7 * $inn[0] + 2 * $inn[1] + 4 * $inn[2] +
                        10 * $inn[3] + 3 * $inn[4] + 5 * $inn[5] +
                        9 * $inn[6] + 4 * $inn[7] + 6 * $inn[8] +
                        8 * $inn[9]
                    ) % 11) % 10);

            $num11 = (string)(((
                        3 * $inn[0] + 7 * $inn[1] + 2 * $inn[2] +
                        4 * $inn[3] + 10 * $inn[4] + 3 * $inn[5] +
                        5 * $inn[6] + 9 * $inn[7] + 4 * $inn[8] +
                        6 * $inn[9] + 8 * $inn[10]
                    ) % 11) % 10);

            return $inn[11] === $num11 && $inn[10] === $num10;
        }
        return false;
    }

    public static function ogrn($attribute, $value, $parameters)
    {

        $ogrn = (int)$value;
        if (!is_numeric($ogrn)) {
            return false;
        }
        if ($ogrn >= PHP_INT_MAX) {
            if (strlen((string)$value) === 13 or strlen((string)$value) === 15) {
                return true;
            } else {
                return false;
            }
        } else {
            $ogrn = (string)$value;

            if (strlen($ogrn) === 13 and $ogrn[12] != substr((substr($ogrn, 0, -1) % 11), -1)
            ) {
                return false;
            } elseif (strlen($ogrn) === 15 and $ogrn[14] != substr(substr($ogrn, 0, -1) % 13, -1)
            ) {
                return false;
            } elseif (strlen($ogrn) != 13 and strlen($ogrn) != 15) {
                return false;
            }
        }
        return true;
    }
}