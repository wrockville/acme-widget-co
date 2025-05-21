<?php
declare(strict_types=1);


namespace Acme\WidgetCo;


use Acme\WidgetCo\Offers\OfferStrategyInterface;

final class Basket
{
    private array $items = [];

    public function __construct(private readonly array $catalog, private readonly array $deliveryRules, private readonly array $offers = [])
    {
        foreach ($this->offers as $code => $strategy) {
            if (!$strategy instanceof OfferStrategyInterface) {
                throw new \InvalidArgumentException("Offer for $code must implement interface `OfferStrategyInterface`");
            }
        }
    }

    /**
     * Add a product to the cart.
     *
     * @param string $code
     *
     * @return void
     */
    public function add(string $code): void
    {
        // Check if the product exists
        if (!isset($this->catalog[$code])) {
            throw new \InvalidArgumentException("Product $code not found");
        }

        // Add product
        $this->items[] = $this->catalog[$code];
    }

    /**
     * The total cost, including delivery fees.
     *
     * @return float
     */
    public function total(): float
    {
        $quantities  = $this->getQuantities();
        $subtotal    = $this->getSubtotal($quantities);
        $deliveryFee = $this->getDeliveryFee($subtotal);

        return (float)bcadd($subtotal, $deliveryFee, 2);
    }

    /**
     * Get the quantity of each product in the catalog.
     *
     * @return array
     */
    private function getQuantities(): array
    {
        $counts = [];

        foreach ($this->items as $product) {
            $counts[$product->code] = ($counts[$product->code] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Calculate the subtotal for the items based on their quantities, prices, and applicable offers.
     *
     * @param array $quantities An associative array where the keys are product codes and the values are quantities.
     *
     * @return string The calculated subtotal as a string formatted to two decimal places.
     * @throws \InvalidArgumentException If an offer is not callable.
     */
    private function getSubtotal(array $quantities): string
    {
        $subtotal = '0.0';

        foreach ($quantities as $code => $qty) {
            $price = number_format($this->catalog[$code]->price, 2, '.', '');

            // Apply offer
            if (isset($this->offers[$code])) {
                $offer = $this->offers[$code];
                if (!is_callable($offer)) {
                    throw new \InvalidArgumentException("Offer for $code must be callable");
                }
                $line = $offer($price, $qty);
            }
            else {
                $line = bcmul($price, (string)$qty, 2);
            }

            $subtotal = bcadd($subtotal, $line, 2);
        }

        return $subtotal;
    }

    /**
     * Calculate the delivery fee based on the provided subtotal and delivery rules.
     *
     * @param string $subtotal The order subtotal to evaluate for delivery fee calculation.
     *
     * @return string The calculated delivery fee, formatted as a string with two decimals.
     */
    private function getDeliveryFee(string $subtotal): string
    {
        foreach ($this->deliveryRules as $threshold => $fee) {
            if ((float)$subtotal < $threshold) {
                return number_format($fee, 2, '.', '');
            }
        }

        return '0.0';
    }
}