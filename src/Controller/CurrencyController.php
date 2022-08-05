<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class CurrencyController extends AbstractController
{
    /**
     * @Route("/", name="currency_compare")
     */
    public function index(): Response
    {
        return $this->render('currency/currency.html.twig');
    }

    /**
     * @Route("/currency/date", name="currency_fetch", methods={"POST|GET"})
     */
    public function getCurrencies(Request $request){
        $todayDate = date('Y-m-d');
        $date = $request->get('date');
        $validDateFormat = \DateTime::createFromFormat('Y-m-d', $date);
        if ($request->getMethod() == 'GET' && $validDateFormat && $date < $todayDate) {

            $client = new CurlHttpClient();
            $keys = ['amount','base','date','rates'];

            $responseUser =  $client->request('GET', 'https://api.frankfurter.app/' . $date . '?base=PLN&symbols=EUR,USD,GBP,CZK');
            $responseToday = $client->request('GET', 'https://api.frankfurter.app/' . $todayDate . '?base=PLN&symbols=EUR,USD,GBP,CZK');

            if($responseUser->getStatusCode() == 200 && $responseToday->getStatusCode() == 200 )
            {
                $currenciesFromUserDate = json_decode($responseUser->getContent(),true);
                $currenciesFromToday = json_decode($responseToday->getContent(),true);

                $diffUser = array_diff($keys, array_keys( $currenciesFromUserDate));
                $diffToday = array_diff($keys, array_keys( $currenciesFromToday));

                if(!count($diffUser) && !count($diffToday)) {
                    return $this->render('currency/currency_table.html.twig', [
                        'currenciesFromUserDate' => $currenciesFromUserDate,
                        'currenciesFromToday' => $currenciesFromToday,
                        'date' => $date
                    ]);
                }
            }
        }
        if (!$validDateFormat) {
            $this->addFlash(
                'notice',
                'Format daty nieprawidłowy'
            );
        } else {
            $this->addFlash(
                'notice',
                'Podałeś nie prawidłową datę lub podana data jest datą dzisiejszą'
            );
        }

        return $this->redirectToRoute('currency_compare');
    }
}
