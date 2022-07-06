<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use App\Models\Currency;
use App\Services\CurrencyConversion;

class CurrencyRates {
    public static function getRates()
    {
        $baseCurrency = CurrencyConversion::getBaseCurrency();

        $url = config('currency_rates.api_url')
            .'?base=' . $baseCurrency->code
            .'&apikey='.config('currency_rates.api_key');
        $client = new Client();
        $response = $client->request('GET', $url); 

        if ($response->getStatusCode() !== 200) {
            throw new Exception('There is a problem with currency rate service');
        }

        $rates = json_decode($response->getBody()->getContents(), true)['rates'];

        foreach (CurrencyConversion::getCurrencies() as $currency) {
            if (!$currency->isMain()) {
                if (!isset($rates[$currency->code])) {
                    throw new Exception('There is a problem with currency ' . $currency->code);
                } else {
                    $currency->update(['rate' => $rates[$currency->code]]);
                    $currency->touch();
                }
            }
        }        


    }
}