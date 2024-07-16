<?php

namespace App\Service;

use DateTime;

class PriceService
{
    const QUARTER_DISCOUNTS = [
        0 => 0,
        1 => ['monthsForDiscount' => 3, 'firstMonth' => 4],
        2 => ['monthsForDiscount' => 5, 'firstMonth' => 10, 'newYear' => 1],
        3 => ['monthsForDiscount' => 3, 'firstMonth' => 14],
    ];


    public function countPrice($postBody)
    {
        $age = $this->getAge($postBody['birthday'], $postBody['startDate']);

        $price = $postBody['price'];

        $price -= $this->getAgeDiscount($age, $price);

        $price -= $this->getQuarterDiscount($postBody['startDate'], $postBody['paymentDate'], $price);

        return $price;
    }
    private function getQuarterDiscount(DateTime $startDate, DateTime $paymentDate, int $price): float|int
    {
        $quarter = $this->getQuarter($startDate);

        [
            'monthsForDiscount' => $monthsForDiscount,
            'firstMonth' => $firstMonth,
        ]  = self::QUARTER_DISCOUNTS[$quarter];




        $year = $paymentDate->format('Y') - isset(self::QUARTER_DISCOUNTS[$quarter]['newYear']);
        $firstMonthDate = DateTime::createFromFormat('d.m.Y', "01.{$firstMonth}.{$year}");

        $monthsDiff = $firstMonthDate->diff($paymentDate)->m;

        if ($monthsDiff >= $monthsForDiscount) {
            $modifier = (2 * ($monthsDiff - $monthsForDiscount));
            $percentage = min(3 + $modifier, 7) / 100;
            return min($price * $percentage, 1500);
        }

        return 0;
    }

    private function getAgeDiscount(int $age, int $price): float|int
    {

        return match (true) {
            $age >= 3 && $age <= 6 => $price * 0.8,
            $age > 6 && $age <= 12 => min(4500, $price * 0.2),
            $age <= 18 => $price * 0.1,
            default => 0,
        };
    }
    private function getAge(DateTime $birthday, DateTime $startDate): int
    {
        return $startDate->diff($birthday)->y;
    }

    private function getQuarter(DateTime $date): int
    {
        $currentDate = new DateTime();
        $isNextYear = $date->diff($currentDate)->y > 1;

        $m = $date->format('m');
        $d = $date->format('d');

        return match(true) {
            $m >= 4 && $m <= 9 && $isNextYear => 1,
            $m > 9 || ($m == 1 && $d <= 14 && $isNextYear) => 2,
            $isNextYear => 3,
            default => 0,
        };
    }
}