<?php

namespace App\Helper;

use DateTime;

class FunctionHelper
{

    public static function startsWith($string, $startString): bool
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public static function in_array_any($needles, $haystack): bool
    {
        return !empty(array_intersect($needles, $haystack));
    }

    public static function findAdresseNumber(?string $adresse)
    {
        if (is_null($adresse)) return null;

        $splitedAdresse = explode(" ", $adresse);
        foreach ($splitedAdresse as $splitedStr) {
            for ($i = 0; $i < strlen($splitedStr); $i++) {
                $char = $splitedStr[$i];
                if (is_numeric($char) || is_int($char)) {
                    return $splitedStr;
                }
            }
        }

        return null;
    }

    public static function moyenne(array $nombres)
    {
        $count = count($nombres);
        if ($count === 0) return 0;
        return (array_sum($nombres) / $count);
    }

    public static function dateDiffMonths(?DateTime $date1, ?DateTime $date2, bool $accepte_negative = true): ?int
    {
        if (is_null($date1) || is_null($date2)) return null;

        $dateInterval = $date1->diff($date2);

        //On prend le nombre de mois et on y ajoute le nombre d'année en mois
        $months = intval($dateInterval->m + $dateInterval->y * 12);

        //On compte un mois en plus si l'interval comprend au moins un jour
        if ($dateInterval->d > 0) {
            $months++;
        }

        if ($dateInterval->invert && $accepte_negative) {
            $signe = "-";
        } else {
            $signe = "+";
        }

        return intval($signe . $months);
    }

    public static function dateDiffDays(?DateTime $date1, ?DateTime $date2, bool $accepte_negative = true): ?int
    {
        if (is_null($date1) || is_null($date2)) return null;

        $dateInterval = $date1->diff($date2);

        if ($dateInterval->invert && $accepte_negative) {
            $signe = "-";
        } else {
            $signe = "+";
        }

        // day = nombre de jour dans l'interval (par ex: 2 mois 5 jours = 5 jours)
        // days = nombre total de jours (par ex: 2 mois 5 jours = 65 jours)
        return intval($signe . $dateInterval->days);
    }

    public static function validationStringLength(string $string, int $length = 255, string $char_remplacement = "...", bool $force_utf8 = false): string
    {
        //Validation
        $string = strlen($string) <= $length ? $string : substr($string, 0, ($length - strlen($char_remplacement))) . $char_remplacement;

        //Utf-8
        if ($force_utf8 && !mb_detect_encoding($string, 'utf-8', true)) {
            $string = utf8_encode($string);
        }

        return $string;
    }

    public static function sortArrayFromArray(array $model, array $array): array
    {
        return array_intersect($model, $array);
    }

}
