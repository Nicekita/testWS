<?php

namespace App\Service;

use DateTime;

class Helper
{

    public static function convertStringToDate(string $dateString): DateTime|false
    {
        return DateTime::createFromFormat('d.m.Y', $dateString);
    }
}