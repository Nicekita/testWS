<?php declare(strict_types=1);

use App\Service\Helper;
use App\Service\PriceService;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{

    const FIELDS = [
        'startDate',
        'birthday',
        'paymentDate',
    ];

    const SAMPLE_REQUESTS = [
        [
            'price' => 1000,
            'startDate' => '15.01.2027',
            'birthday' => '21.01.2000',
            'paymentDate' => '21.10.2026',
            'expectedPrice' => 970
        ],
        [
            'price' => 1000,
            'startDate' => '15.01.2027',
            'birthday' => '21.01.2010',
            'paymentDate' => '21.10.2026',
            'expectedPrice' => 873
        ],
        [
            'price' => 1000,
            'startDate' => '15.01.2027',
            'birthday' => '21.01.2022',
            'paymentDate' => '21.10.2026',
            'expectedPrice' => 194
        ],
        [
            'price' => 1000,
            'startDate' => '02.01.2022',
            'birthday' => '01.01.2019',
            'paymentDate' => '01.01.2022',
            'expectedPrice' => 200
        ],
    ];
    public function testPrice(): void
    {
        $priceService = new PriceService();
        foreach (self::SAMPLE_REQUESTS as $request) {
            $request = $this->convertDates($request);
            $price = $priceService->countPrice($request);
            $this->assertEquals($request['expectedPrice'], $price);
        }
    }


    private function convertDates($body)
    {
        foreach (self::FIELDS as $field) {
            $body[$field] = Helper::convertStringToDate($body[$field]);
        }
        return $body;
    }
}