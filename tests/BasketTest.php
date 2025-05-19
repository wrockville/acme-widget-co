<?php
declare(strict_types=1);


namespace Acme\WidgetCo\Tests;

use PHPUnit\Framework\TestCase;
use Acme\WidgetCo\{Basket, Product};

class BasketTest extends TestCase
{
    private array $catalog;
    private array $deliveryRules;
    private array $offers;

    protected function setUp(): void
    {
        $this->catalog = [
            'R01' => new Product('R01', 'Red Widget', 32.95),
            'G01' => new Product('G01', 'Green Widget', 24.95),
            'B01' => new Product('B01', 'Blue Widget', 7.95),
        ];

        $this->deliveryRules = [
            90 => 0.00,
            50 => 2.95,
            0  => 4.95,
        ];

        $this->offers = [
            'R01' => function (array $items): float {
                if (empty($items)) {
                    return 0.0;
                }
                $count = count($items);
                $price = $items[0]->price ?? 0;
                return floor($count / 2) * ($price / 2);
            },
        ];
    }

    public function test_total_with_b01_and_g01(): void
    {
        $basket = new Basket($this->catalog, $this->deliveryRules, $this->offers);
        $basket->add('B01');
        $basket->add('G01');
        $this->assertEquals(37.85, $basket->total());
    }
}