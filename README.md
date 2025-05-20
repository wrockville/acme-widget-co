# acme-widget-co - Challenge

This is a proof-of-concept project for Acme Widget Co.

## Requirements:

* PHP 8.4
* ext-json
* ext-bcmath
* Composer
* PHPUnit

## Features

- Add products to a basket
- Apply special offers using the Strategy Pattern
- Calculate total price including delivery
- Modular, extensible architecture with clean separation of concerns
- Unit tested with PHPUnit

## Products

| SKU  | Name         | Price  |
|------|--------------|--------|
| R01  | Red Widget   | $32.95 |
| G01  | Green Widget | $24.95 |
| B01  | Blue Widget  | $7.95  |

## Business Rules

- A customer buys two R01s; the second one is half price.
- Delivery charges:
    - Orders under \$50: \$4.95
    - Orders between \$50 and \$90: $2.95
    - Orders over $90: free delivery

## Design

- **Strategy Pattern** is used to apply offers. Offers are defined as classes implementing `OfferStrategyInterface`.
- Each product offer is injected into the basket logic, keeping business rules separate from basket logic.
- Monetary values are calculated using `bcmath` for precision.
- Delivery rules are configurable via a threshold-based array.

## Folder Structure

```
acme-widget-co/
├── src/
│   ├── Basket.php
│   ├── Product.php
│   └── Offers/
│       ├── OfferStrategyInterface.php
│       └── RedWidgetDiscount.php
├── tests/
│   ├── BasketTest.php
│   └── Offers/
│       └── RedWidgetDiscountTest.php
├── composer.json
└── README.md
```

## Usage:

* Clone the repository:
   ```bash
   git clone https://github.com/wrockville/acme-widget-co.git
   ```

* Navigate to the project directory: 
   ```bash
   cd acme-widget-co 
   ```

* Install dependencies:
   ```bash
   composer install
   ```

* Run tests:
   ```bash
   ./vendor/bin/phpunit tests
   ```

## How It Works

1. You instantiate a `Basket` with a catalog, delivery rules, and offer strategies.
2. You add products to the basket using `add('SKU')`.
3. The `total()` method calculates the subtotal, applies relevant offers, adds delivery, and returns the final total.

Example:
```php
$basket = new Basket($catalog, $deliveryRules, $offers);
$basket->add('R01');
$basket->add('R01');
echo $basket->total(); // 54.37
```

## Assumptions

- Input product codes are always valid and exist in the catalog.
- All prices are in USD and use dot notation (e.g., 32.95).
- The basket operates in a stateless context (no user sessions or persistence).
- Offers are hardcoded but easily extensible through the strategy interface.

## Notes

* Fully PSR-4 compliant
* Optimized for extensibility (e.g., adding more offers or dynamic delivery pricing)
* Assumes clean input – no validation is done on product IDs or types
* The project is not optimized for performance or security.
* The project is not intended for use in a production environment.

## Acknowledgements

This project is a proof-of-concept for a coding challenge. It was created by [Whitney Rockville](https://github.com/wrockville).