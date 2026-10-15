<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Tests\Fixtures;

use Brick\Money\Money;
use Givanov95\Shipping\Contracts\CreatesWaybills;
use Givanov95\Shipping\Contracts\DeliveryMethod;
use Givanov95\Shipping\Contracts\Parcel;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\Exceptions\WaybillNotCreated;
use Givanov95\Shipping\Shipment;
use Givanov95\Shipping\Waybill;

/**
 * A courier that takes parcels to its offices and refuses one heavier than 30 kg.
 */
final class CourierWithWaybills implements DeliveryMethod, CreatesWaybills
{
    public function key(): string
    {
        return 'courier';
    }

    public function destinationTypes(): array
    {
        return [DestinationType::Office];
    }

    public function rules(DestinationType $type): array
    {
        return ['point_code' => ['required', 'string']];
    }

    public function destination(DestinationType $type, array $choice): Destination
    {
        return new Destination(method: $this->key(), type: $type, countryCode: $choice['country_code'], pointCode: $choice['point_code']);
    }

    public function price(Destination $destination, Parcel $parcel): ?Money
    {
        return Money::of(5, $parcel->getCurrency());
    }

    public function createWaybill(Destination $destination, Shipment $shipment): Waybill
    {
        if ($shipment->weight > 30) {
            throw new WaybillNotCreated('The parcel is heavier than 30 kg.');
        }

        $number = sprintf('%s-%s', $destination->pointCode, $shipment->reference);

        return new Waybill(
            number: $number,
            trackingUrl: 'https://courier.test/track/'.$number,
            labelUrl: 'https://courier.test/labels/'.$number.'.pdf',
            price: Money::of(5, $shipment->cashOnDelivery?->getCurrency() ?? 'EUR'),
        );
    }
}
