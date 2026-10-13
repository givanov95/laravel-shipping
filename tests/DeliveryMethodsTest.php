<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Tests;

use Brick\Money\Money;
use Givanov95\Shipping\Contracts\DeliveryMethod;
use Givanov95\Shipping\DeliveryMethods;
use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\ShippingServiceProvider;
use Givanov95\Shipping\Tests\Fixtures\Basket;
use Givanov95\Shipping\Tests\Fixtures\PickupFromShop;
use Givanov95\Shipping\Tests\Fixtures\Warehouse;
use InvalidArgumentException;
use LogicException;
use Orchestra\Testbench\TestCase;
use stdClass;

final class DeliveryMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Warehouse::$built = 0;
    }

    protected function getPackageProviders($app): array
    {
        return [ShippingServiceProvider::class];
    }

    public function test_the_application_has_one_registry(): void
    {
        $this->assertSame($this->app->make(DeliveryMethods::class), $this->app->make(DeliveryMethods::class));
    }

    public function test_a_registered_method_is_found_by_its_key(): void
    {
        $methods = $this->methods();
        $methods->register('pickup', PickupFromShop::class, 'Pick up from the shop');

        $this->assertTrue($methods->has('pickup'));
        $this->assertInstanceOf(PickupFromShop::class, $methods->get('pickup'));
        $this->assertSame(['pickup'], $methods->keys());
        $this->assertSame(['pickup' => 'Pick up from the shop'], $methods->labels());
    }

    public function test_nothing_is_found_under_a_key_that_is_not_registered(): void
    {
        $methods = $this->methods();

        $this->assertFalse($methods->has('pigeon'));
        $this->assertNull($methods->get('pigeon'));
        $this->assertSame([], $methods->keys());
        $this->assertSame([], $methods->labels());
    }

    /**
     * Registering costs nothing: the method and what it needs are built when it is asked for.
     */
    public function test_a_method_is_built_by_the_container_when_it_is_asked_for(): void
    {
        $methods = $this->methods();
        $methods->register('pickup', PickupFromShop::class, 'Pick up from the shop');
        $methods->labels();

        $this->assertSame(0, Warehouse::$built);

        $method = $methods->get('pickup');

        $this->assertSame(1, Warehouse::$built);
        $this->assertInstanceOf(PickupFromShop::class, $method);
        $this->assertSame('The shop', $method->warehouse->name);
    }

    public function test_the_methods_keep_the_order_they_were_registered_in(): void
    {
        $methods = $this->methods();
        $methods->register('pickup', PickupFromShop::class, 'Pick up from the shop');
        $methods->register('100', PickupFromShop::class, 'A hundred');

        $this->assertSame(['pickup', '100'], $methods->keys());
    }

    public function test_a_second_registration_under_a_key_replaces_the_first(): void
    {
        $methods = $this->methods();
        $methods->register('pickup', PickupFromShop::class, 'Pick up from the shop');
        $methods->register('pickup', PickupFromShop::class, 'Collect in person');

        $this->assertSame(['pickup' => 'Collect in person'], $methods->labels());
    }

    public function test_a_class_that_is_not_a_delivery_method_is_refused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(stdClass::class.' does not implement '.DeliveryMethod::class);

        $this->methods()->register('pigeon', stdClass::class, 'Pigeon');
    }

    /**
     * The key is what an order keeps and what the method writes in its Destination.
     */
    public function test_a_method_registered_under_another_key_than_its_own_is_refused(): void
    {
        $methods = $this->methods();
        $methods->register('collect', PickupFromShop::class, 'Collect in person');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('is registered as "collect" but calls itself "pickup"');

        $methods->get('collect');
    }

    public function test_a_method_gives_a_destination_and_a_price_for_a_parcel(): void
    {
        $methods = $this->methods();
        $methods->register('pickup', PickupFromShop::class, 'Pick up from the shop');
        $method = $methods->get('pickup');

        $destination = $method->destination(DestinationType::Office, ['country_code' => 'BG']);

        $this->assertSame('pickup', $destination->method);
        $this->assertSame('The shop', $destination->pointName);
        $this->assertTrue($method->price($destination, new Basket('EUR'))->isEqualTo(Money::zero('EUR')));
    }

    private function methods(): DeliveryMethods
    {
        return $this->app->make(DeliveryMethods::class);
    }
}
