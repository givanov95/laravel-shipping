<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Contracts;

use Givanov95\Shipping\Destination;
use Givanov95\Shipping\Exceptions\WaybillNotCreated;
use Givanov95\Shipping\Shipment;
use Givanov95\Shipping\Waybill;

/**
 * For a method that hands a parcel to a courier: it creates the waybill for a packed order.
 * A method without it is delivered by the shop itself, or handed to the courier by hand.
 */
interface CreatesWaybills
{
    /**
     * @param  Destination  $destination  the one the method built at checkout, as the shop stored it
     *
     * @throws WaybillNotCreated when the courier refuses the parcel or does not answer
     */
    public function createWaybill(Destination $destination, Shipment $shipment): Waybill;
}
