<?php

namespace App\Controller;

use App\Service\Helper;
use App\Service\PriceService;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CountController
{

    const FIELDS = [
        'price' => 'number',
        'startDate' => 'date',
        'birthday' => 'date',
        'paymentDate' => 'date',
    ];

    const QUARTER_DISCOUNTS = [
        0 => 0,
        1 => ['monthsForDiscount' => 3, 'firstMonth' => 4],
        2 => ['monthsForDiscount' => 5, 'firstMonth' => 10],
        3 => ['monthsForDiscount' => 3, 'firstMonth' => 1],
    ];

    private PriceService $service;
    public function __construct()
    {
        $this->service = new PriceService();
    }


    #[Route('/count', name: 'count', methods: ['POST'])]
    public function index(): JsonResponse
    {
        $request = Request::createFromGlobals();

        $postBody = json_decode($request->getContent(), true);

        $error = $this->validateRequest($postBody);

        if ($error) {
            return new JsonResponse(['error' => $error], 400);
        }

        $postBody = $this->convertDates($postBody);

        $price = $this->service->countPrice($postBody);

        return new JsonResponse(['price' => $price]);
    }


    private function validateRequest(array $data): ?string
    {
        foreach (self::FIELDS as $field => $type) {
            if (!isset($data[$field])) {
                return 'Поле ' . $field . ' обязательно';
            }
            if ($type === 'number' && !is_numeric($data[$field])) {
                return 'Поле ' . $field . ' должно быть числом';
            }

            if ($type === 'date' && !Helper::convertStringToDate($data[$field])) {
                return 'Поле ' . $field . ' должно быть датой в формате dd.mm.yy';
            }

        }
        return false;
    }

    private function convertDates($postBody)
    {
        foreach (self::FIELDS as $field => $type) {
            if ($type === 'date') {
                $postBody[$field] = Helper::convertStringToDate($postBody[$field]);
            }
        }
        return $postBody;
    }


}