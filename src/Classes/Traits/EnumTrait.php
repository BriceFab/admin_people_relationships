<?php

namespace App\Classes\Traits;

use ReflectionClass;

trait EnumTrait
{

    public static function getList(array $sauf = []): array
    {
        $class = new ReflectionClass(self::class);
        $items = $class->getConstants();

        if (!is_null($sauf) && count($sauf) > 0) {
            $items = array_filter($items, function ($item) use ($sauf) {
                return !in_array($item, $sauf);
            });
        }

        return $items;
    }

    public static function getChoices(string $transKey = "list.choice", array $sauf = []): array
    {
        $choices = [];

        foreach (self::getList($sauf) as $item) {
            $choices[strtolower("$transKey.$item")] = $item;
        }

        return $choices;
    }

    public static function addListChoice(array &$choies, string $enum, string $transKey = "list.choice")
    {
        $choies["$transKey." . strtolower($enum)] = $enum;
    }

}