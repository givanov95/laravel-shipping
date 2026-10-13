<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Contracts;

use Givanov95\Shipping\DestinationType;
use Givanov95\Shipping\Place;
use Givanov95\Shipping\Point;
use Givanov95\Shipping\Street;

/**
 * For a method that delivers to places it knows by id: the customer picks the place, then
 * one of its points or a street, from what the courier lists. A courier that is down gives
 * an empty list, not an exception.
 */
interface PlaceDirectory
{
    /**
     * @return list<Place>
     */
    public function places(string $search): array;

    /**
     * @param  DestinationType  $type  an office or a locker
     * @return list<Point>
     */
    public function points(string $placeId, DestinationType $type): array;

    /**
     * @return list<Street>
     */
    public function streets(string $placeId, string $search): array;
}
