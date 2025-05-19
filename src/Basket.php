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
        // Compute item counts
        $counts = [];
        foreach ($this->items as $product) {
            $counts[$product->code] = ($counts[$product->code] ?? 0) + 1;
        }

        // Compute subtotal
        $subtotal = "0.0";
        foreach ($counts as $code => $qty) {
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

        // Add delivery fee
        $deliveryFee = "0.0";
        foreach ($this->deliveryRules as $threshold => $fee) {
            if ((float)$subtotal < $threshold) {
                $deliveryFee = number_format($fee, 2, '.', '');
                break;
            }
        }

        return (float)bcadd($subtotal, $deliveryFee, 2);
    }
}