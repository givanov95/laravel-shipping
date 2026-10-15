# Laravel Shipping

The delivery contract between a Laravel shop and its delivery methods. A courier, a flat rate or a pickup point is one class behind `DeliveryMethod`; the shop asks it what the customer has to choose, turns the choice into a `Destination` and asks for its price. The shop needs no branch, table or form per courier, and a courier package needs nothing of the shop.

```bash
composer require givanov95/laravel-shipping
```

> **0.x:** the contract may still change between minor versions until a courier behind it has been checked against its real API.

## A delivery method

```php
use Brick\Money\Money;
use Givanov95\Shipping\Contracts\DeliveryMethod;
use Givanov95\Shipping\Contracts\Parcel;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;

final class PickupFromShop implements DeliveryMethod
{
    public function key(): string { return 'pickup'; }

    public function destinationTypes(): array { return [DestinationType::Office]; }

    public function rules(DestinationType $type): array { return []; }

    public function destination(DestinationType $type, array $choice): Destination
    {
        return new Destination(
            method: $this->key(),
            type: $type,
            countryCode: $choice['country_code'],
            pointCode: 'SHOP',
            pointName: 'The shop',
        );
    }

    public function price(Destination $destination, Parcel $parcel): ?Money
    {
        return Money::zero($parcel->getCurrency());
    }
}
```

| Method | What the shop does with it |
| --- | --- |
| `key()` | Keeps it on the order. It is the key the method is registered under. |
| `destinationTypes()` | Offers the customer `address`, `office` and/or `locker`. |
| `rules($type)` | Validates the checkout form. The fields are the same for every method: `city`, `postal_code`, `street`, `street_number`, `note`, `place_id`, `street_id`, `point_code`, `point_name`. |
| `destination($type, $choice)` | Stores the `Destination` it gets back. `$choice` is validated with `rules()`; the shop fills in `country_code`. |
| `price($destination, $parcel)` | Charges it. `null` means the method has no price for this destination. The shop has no exchange rate: a price in another currency than the one of the parcel is refused, not converted. |

`Destination` carries the method, the type, the country code (ISO 3166-1 alpha-2), city, postal code, street, number, note, and the code and name of an office or a locker. Whatever else the courier needs later (the id of a city, of a street) goes in `courierData`, which only the method reads. `toArray()` gives the fields in snake_case, ready for a table.

`Parcel` is what is delivered. Today it tells the currency the customer pays in; the cart of the shop implements it.

## Optional contracts

- **`PlaceDirectory`** — for a method that delivers to places it knows by id. `places($search)`, `points($placeId, $type)` and `streets($placeId, $search)` return `Place`, `Point` and `Street` objects for the checkout form. A courier that is down gives an empty list, not an exception.
- **`DeliveryCountries`** — for a method that says where it delivers. `countries($currency)` lists the countries for a parcel in that currency (none: the method is not offered), and `freeDeliveryApplies($destination)` says whether the free delivery threshold of the shop holds there. A method without it delivers in the country of the shop.
- **`CreatesWaybills`** — for a method that hands the parcel to a courier. `createWaybill($destination, $shipment)` creates the waybill of a packed order and returns a `Waybill`. When the courier refuses the shipment or does not answer it throws `Exceptions\WaybillNotCreated`, with a message for the person who asked. A method without it is delivered by the shop, or handed to the courier by hand.

### Waybills

`Parcel` is what is known in the cart, before anything is packed: the currency, to name a price. A `Shipment` is what is known once the order is packed, and what the courier needs to take it:

| Field | |
| --- | --- |
| `reference` | The number of the order, printed on the waybill. |
| `recipientName`, `recipientPhone`, `recipientEmail` | Who receives it; the email may be null. |
| `weight` | The total weight in kilograms, above zero. |
| `packages` | How many packages, at least one. |
| `contents` | What is inside. |
| `cashOnDelivery` | A `Money` the courier collects from the recipient, in its currency, or null. |

The destination is the `Destination` the method built at checkout, as the shop stored it, so whatever the method put in `courierData` comes back to it. The `Waybill` carries the `number` and, when the courier gives them, a `trackingUrl` for the recipient, a `labelUrl` to print for the shop and the `price` the courier charges.

## Registering a method

The package binds one `DeliveryMethods` registry. A package that brings a method adds it from its service provider:

```php
use Givanov95\Shipping\DeliveryMethods;
use Illuminate\Support\ServiceProvider;

final class PickupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->callAfterResolving(DeliveryMethods::class, function (DeliveryMethods $methods): void {
            $methods->register('pickup', PickupFromShop::class, 'Pick up from the shop');
        });
    }
}
```

The class is built by the container the first time the method is asked for, so its dependencies are injected and a request that never reaches the checkout pays nothing. The label is what the shop calls the method; the shop translates it.

Registering a method does not offer it. Which methods a customer can pick, and in what order, is the decision of the shop:

```php
$methods = app(DeliveryMethods::class);

$methods->has('pickup');    // true
$methods->get('pickup');    // the PickupFromShop instance, or null for an unknown key
$methods->keys();           // ['pickup'], in the order of registration
$methods->labels();         // ['pickup' => 'Pick up from the shop']
```

`get()` throws a `LogicException` when the class says another `key()` than the one it is registered under: an order would name one method and its delivery another.

## Testing

```bash
composer test
```

## License

MIT
