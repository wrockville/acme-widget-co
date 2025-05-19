<?php
declare(strict_types=1);


namespace Acme\WidgetCo\Offers;


interface OfferStrategyInterface
{
    /**
     * Apply the offer logic to the price and quantity.
     *
     * @param string $price Unit price of the item (as string for BCMath precision)
     * @param int    $qty   Quantity of items
     *
     * @return string Total price after offer applied
     */
    public function __invoke(string $price, int $qty): string;
}