<?php
declare(strict_types=1);

namespace Acme\WidgetCo\Tests\Offers;

use PHPUnit\Framework\TestCase;
use Acme\WidgetCo\Offers\RedWidgetDiscount;

final class RedWidgetDiscountTest extends TestCase
{
    public function test_applies_discount_correctly(): void
    {
        $offer     = new RedWidgetDiscount();
        $unitPrice = '32.95';

        // 2 units → one full price, one half price
        $total = $offer($unitPrice, 2);
        $this->assertSame('49.42', $total);

        // 3 units → two full prices, one half price
        $total = $offer($unitPrice, 3);
        $this->assertSame('82.37', $total);

        // 1 unit → no discount
        $total = $offer($unitPrice, 1);
        $this->assertSame('32.95', $total);
    }
}