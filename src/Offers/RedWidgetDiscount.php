<?php
declare(strict_types=1);


namespace Acme\WidgetCo\Offers;


final class RedWidgetDiscount implements OfferStrategyInterface
{
    public function __invoke(string $price, int $qty): string
    {
        $fullPriceQty = intdiv($qty, 2) + ($qty % 2);
        $halfPriceQty = intdiv($qty, 2);

        $full = bcmul($price, (string)$fullPriceQty, 2);
        $half = bcmul($price, '0.5', 2);
        $half = bcmul($half, (string)$halfPriceQty, 2);

        return bcadd($full, $half, 2);
    }
}