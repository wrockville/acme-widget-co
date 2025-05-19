<?php
declare(strict_types=1);


namespace Acme\WidgetCo;


final class Basket
{
    private array $items = [];

    public function __construct(private array $catalog, private array $deliveryRules, private array $offers = []) {}

    public function add(string $code): void
    {
        if (!isset($this->catalog[$code])) {
            throw new \InvalidArgumentException("Product $code not found");
        }
        $this->items[] = $code;
    }

    public function total(): float
    {
        // Calculate subtotal
        $subtotal = 0.0;
        foreach ($this->items as $code) {
            $subtotal += $this->catalog[$code]?->price ?? 0.0;
        }

        // Apply offers
        $discount = 0.0;
        foreach ($this->offers as $offer) {
            $discount += $offer($this->items);
        }

        $total = $subtotal - $discount;

        // Apply delivery fee
        foreach ($this->deliveryRules as $threshold => $fee) {
            if ($total > $threshold) {
                $total += $fee;
                break;
            }
        }

        return round($total, 2);
    }
}