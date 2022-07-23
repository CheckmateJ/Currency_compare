<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class CurrencyController extends AbstractController
{
    /**
     * @Route("/", name="currency_table")
     */
    public function index(): Response
    {

        return $this->render('currency/currency.html.twig');
    }

    /**
     * @Route("/currency/date", name="currency_fetch", methods={"POST|GET"})
     */
    public function getCurrencies(Request $request){
        $date = json_decode($request->getContent());
        $todayDate = date('Y-m-d');
        $currenciesFromUserDate = file_get_contents('https://api.frankfurter.app/' .$date. '?base=PLN&symbols=EUR,USD,GBP,CZK');
        $currenciesFromToday = file_get_contents('https://api.frankfurter.app/' .$todayDate. '?base=PLN&symbols=EUR,USD,GBP,CZK');
        return new JsonResponse([$currenciesFromUserDate, $currenciesFromToday], 200);
    }
}
