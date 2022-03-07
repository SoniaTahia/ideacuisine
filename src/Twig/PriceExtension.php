<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PriceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('price', [$this, 'price']),
        ];
    }

   /* public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }*/

    public function price(int $value, string $symbol  = 'TND', string $decSep = ',', string $thousSep = ' ')
    {
        // ...
        $price = $value / 1000;
        $price = number_format($price, 3, $decSep, $thousSep);
        return "$price $symbol";
    }
}
