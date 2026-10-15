<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Tests;

use Brick\Money\Money;
use Givanov95\Shipping\Contracts\CreatesWaybills;
use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\Exceptions\WaybillNotCreated;
use Givanov95\Shipping\Shipment;
use Givanov95\Shipping\Tests\Fixtures\CourierWithWaybills;
use Givanov95\Shipping\Tests\Fixtures\PickupFromShop;
use Givanov95\Shipping\Tests\Fixtures\Warehouse;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WaybillTest extends TestCase
{
    public function test_a_courier_gives_a_waybill_for_a_shipment(): void
    {
        $waybill = (new CourierWithWaybills())->createWaybill($this->office(), $this->shipment(cashOnDelivery: Money::of('42.50', 'EUR')));

        $this->assertSame('1042-1001', $waybill->number);
        $this->assertSame('https://courier.test/track/1042-1001', $waybill->trackingUrl);
        $this->assertSame('https://courier.test/labels/1042-1001.pdf', $waybill->labelUrl);
        $this->assertTrue(Money::of(5, 'EUR')->isEqualTo($waybill->price));
    }

    public function test_a_courier_that_refuses_says_why(): void
    {
        $this->expectException(WaybillNotCreated::class);
        $this->expectExceptionMessage('heavier than 30 kg');

        (new CourierWithWaybills())->createWaybill($this->office(), $this->shipment(weight: 31));
    }

    public function test_creating_waybills_is_optional(): void
    {
        $this->assertInstanceOf(CreatesWaybills::class, new CourierWithWaybills());
        $this->assertNotInstanceOf(CreatesWaybills::class, new PickupFromShop(new Warehouse()));
    }

    public function test_a_shipment_without_cash_on_delivery_collects_nothing(): void
    {
        $shipment = $this->shipment();

        $this->assertNull($shipment->cashOnDelivery);
        $this->assertSame(1, $shipment->packages);
    }

    public function test_a_shipment_weighs_something(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->shipment(weight: 0);
    }

    public function test_a_shipment_has_a_package(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->shipment(packages: 0);
    }

    private function office(): Destination
    {
        return new Destination(method: 'courier', type: DestinationType::Office, countryCode: 'BG', pointCode: '1042', pointName: 'Center');
    }

    private function shipment(float $weight = 1.5, int $packages = 1, ?Money $cashOnDelivery = null): Shipment
    {
        return new Shipment(
            reference: '1001',
            recipientName: 'Jane Roe',
            recipientPhone: '+359888000000',
            recipientEmail: 'jane@example.com',
            weight: $weight,
            packages: $packages,
            contents: 'Shirt',
            cashOnDelivery: $cashOnDelivery,
        );
    }
}
