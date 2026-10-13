<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Tests;

use Givanov95\Shipping\Destination;
use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\Place;
use Givanov95\Shipping\Point;
use Givanov95\Shipping\Street;
use PHPUnit\Framework\TestCase;

final class DestinationTest extends TestCase
{
    public function test_a_destination_is_an_array_of_snake_case_fields(): void
    {
        $destination = new Destination(
            method: 'courier',
            type: DestinationType::Address,
            countryCode: 'BG',
            city: 'Sofia',
            postalCode: '1000',
            street: 'Main',
            streetNumber: '7',
            note: 'Ring twice',
            courierData: ['city_id' => 41],
        );

        $this->assertSame([
            'method'        => 'courier',
            'type'          => 'address',
            'country_code'  => 'BG',
            'city'          => 'Sofia',
            'postal_code'   => '1000',
            'street'        => 'Main',
            'street_number' => '7',
            'note'          => 'Ring twice',
            'point_code'    => null,
            'point_name'    => null,
            'courier_data'  => ['city_id' => 41],
        ], $destination->toArray());
    }

    public function test_a_destination_without_courier_data_stores_none(): void
    {
        $destination = new Destination(method: 'courier', type: DestinationType::Locker, countryCode: 'BG', pointCode: 'L-1', pointName: 'Locker 1');

        $this->assertNull($destination->toArray()['courier_data']);
        $this->assertSame('L-1', $destination->toArray()['point_code']);
    }

    public function test_an_office_and_a_locker_are_points_and_an_address_is_not(): void
    {
        $this->assertFalse(DestinationType::Address->isPoint());
        $this->assertTrue(DestinationType::Office->isPoint());
        $this->assertTrue(DestinationType::Locker->isPoint());
    }

    public function test_what_a_directory_lists_is_an_array_for_the_form(): void
    {
        $this->assertSame(['id' => '41', 'name' => 'Sofia', 'postal_code' => '1000'], (new Place('41', 'Sofia', '1000'))->toArray());
        $this->assertSame(['id' => 'Sofia', 'name' => 'Sofia', 'postal_code' => null], (new Place('Sofia', 'Sofia'))->toArray());
        $this->assertSame(['code' => '1042', 'name' => 'Center'], (new Point('1042', 'Center'))->toArray());
        $this->assertSame(['id' => '9', 'name' => 'Main'], (new Street('Main', '9'))->toArray());
        $this->assertSame(['id' => null, 'name' => 'Main'], (new Street('Main'))->toArray());
    }
}
