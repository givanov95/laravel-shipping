<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Tests\Fixtures;

use Brick\Money\Money;
use Givanov95\Shipping\Contracts\DeliveryMethod;
use Givanov95\Shipping\Contracts\Parcel;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;

/**
 * The smallest method there is: the order is picked up from the shop, for free.
 */
final class PickupFromShop implements DeliveryMethod
{
    public function __construct(public readonly Warehouse $warehouse)
    {
    }

    public function key(): string
    {
        return 'pickup';
    }

    public function destinationTypes(): array
    {
        return [DestinationType::Office];
    }

    public function rules(DestinationType $type): array
    {
        return [];
    }

    public function destination(DestinationType $type, array $choice): Destination
    {
        return new Destination(
            method: $this->key(),
            type: $type,
            countryCode: $choice['country_code'],
            pointCode: 'SHOP',
            pointName: $this->warehouse->name,
        );
    }

    public function price(Destination $destination, Parcel $parcel): ?Money
    {
        return Money::zero($parcel->getCurrency());
    }
}
