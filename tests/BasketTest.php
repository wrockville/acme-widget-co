<?php
declare(strict_types=1);


namespace Acme\WidgetCo\Tests;

use PHPUnit\Framework\TestCase;
use Acme\WidgetCo\{Basket, Offers\RedWidgetDiscount, Product};

final class BasketTest extends TestCase
{
    private array $catalog;
    private array $deliveryRules;
    private array $offers;

    /**
     * Initializes and sets up the required data for the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->catalog = [
            'R01' => new Product('R01', 'Red Widget', 32.95),
            'G01' => new Product('G01', 'Green Widget', 24.95),
            'B01' => new Product('B01', 'Blue Widget', 7.95),
        ];

        $this->deliveryRules = [
            50          => 4.95,
            90          => 2.95,
            PHP_INT_MAX => 0.0,
        ];
        ksort($this->deliveryRules);

        $this->offers = [
            'R01' => new RedWidgetDiscount(),
        ];
    }

    /**
     * Tests the total calculation of the basket when products B01 and G01 are added.
     *
     * @return void
     */
    public function test_total_with_b01_and_g01(): void
    {
        $basket = new Basket($this->catalog, $this->deliveryRules, $this->offers);
        $basket->add('B01');
        $basket->add('G01');
        $this->assertEquals(37.85, $basket->total());
    }

    /**
     * Tests the total calculation of the basket when product R01 is added twice.
     *
     * @return void
     */
    public function test_total_with_two_r01(): void
    {
        $basket = new Basket($this->catalog, $this->deliveryRules, $this->offers);
        $basket->add('R01');
        $basket->add('R01');
        $this->assertEquals(54.37, $basket->total());
    }

    /**
     * Tests the total calculation of the basket when products R01 and G01 are added.
     *
     * @return void
     */
    public function test_total_with_r01_and_g01(): void
    {
        $basket = new Basket($this->catalog, $this->deliveryRules, $this->offers);
        $basket->add('R01');
        $basket->add('G01');
        $this->assertEquals(60.85, $basket->total());
    }

    /**
     * Tests the total calculation of the basket when multiple products are added.
     *
     * @return void
     */
    public function test_total_with_multiple_products(): void
    {
        $basket = new Basket($this->catalog, $this->deliveryRules, $this->offers);
        $basket->add('B01');
        $basket->add('B01');
        $basket->add('R01');
        $basket->add('R01');
        $basket->add('R01');
        $this->assertEquals(98.27, $basket->total());
    }
}