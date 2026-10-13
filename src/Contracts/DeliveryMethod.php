<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Contracts;

use Brick\Money\Money;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A way to deliver an order. The shop knows a method only through this: it asks what the
 * customer has to choose, turns the choice into a Destination and asks for its price.
 * The method translates the courier to the contract, not to the database - the shop stores
 * the Destination itself.
 */
interface DeliveryMethod
{
    /**
     * The key the method is registered under (DeliveryMethods::register()). An order keeps it.
     */
    public function key(): string;

    /**
     * @return list<DestinationType>
     */
    public function destinationTypes(): array;

    /**
     * The rules for the choice of the customer when it is of the given type.
     *
     * The fields are the same for every method: `city`, `postal_code`, `street`, `street_number`,
     * `note`, `place_id`, `street_id`, `point_code` and `point_name`.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(DestinationType $type): array;

    /**
     * @param  array<string, mixed>  $choice  validated with rules(), with `country_code` filled in by the shop
     */
    public function destination(DestinationType $type, array $choice): Destination;

    /**
     * The price in the currency the method charges in; the shop refuses one that is not the
     * currency of the parcel. Null when the method has no price for this destination.
     */
    public function price(Destination $destination, Parcel $parcel): ?Money;
}
