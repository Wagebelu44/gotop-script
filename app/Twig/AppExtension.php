<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('ratesRounding', [$this, 'ratesRounding']),
            new TwigFunction('currencyFormat', [$this, 'currencyFormat']),
            new TwigFunction('timezone', [$this, 'timezone']),
        ];
    }

    public function ratesRounding($setting, $rate)
    {
        if ($setting == 'Ones (1)') {
            return round($rate);
        } elseif ($setting == 'Hundredth (1.11)') {
            return number_format($rate, 2);
        } elseif ($setting == 'Thousandth (1.111)') {
            return number_format($rate, 3);
        }
        return $rate;
    }

    public function currencyFormat($setting, $amount)
    {
        if ($setting == '1000.00') {
            return number_format($amount, 2, '.', '');
        } elseif ($setting == '1000,00') {
            return number_format($amount, 2, ',', '');
        } elseif ($setting == '1,000.12') {
            return number_format($amount, 2, '.', ',');
        } elseif ($setting == '1,000') {
            return number_format($amount, 0);
        }
        return $amount;
    }

    public function timezone($setting, $date)
    {
        if ($setting != '') {
            return date('Y-m-d H:i:s', (strtotime($date)+$setting));
        }
        return date('Y-m-d H:i:s', strtotime($date));
    }
}