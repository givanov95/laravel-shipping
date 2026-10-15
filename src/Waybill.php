<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

use Brick\Money\Money;

/**
 * What the courier gives back for a shipment it took.
 */
final readonly class Waybill
{
    /**
     * @param  string  $number  the number of the waybill, the one the recipient tracks the parcel by
     * @param  string|null  $trackingUrl  a page where the recipient follows the parcel
     * @param  string|null  $labelUrl  the label to print and stick on the parcel; for the shop, not the recipient
     * @param  Money|null  $price  what the courier charges the shop, when it says
     */
    public function __construct(
        public string $number,
        public ?string $trackingUrl = null,
        public ?string $labelUrl = null,
        public ?Money $price = null,
    ) {
    }
}
