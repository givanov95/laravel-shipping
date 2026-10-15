<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

use Brick\Money\Money;
use InvalidArgumentException;

/**
 * A packed order, as the courier has to know it to take it: who receives it, how heavy it is
 * and how much the courier collects. Parcel is what is known in the cart, before anything is
 * packed; a Shipment is known only once it is.
 */
final readonly class Shipment
{
    /**
     * @param  string  $reference  the number of the order, printed on the waybill
     * @param  float  $weight  the total weight in kilograms
     * @param  int  $packages  how many packages the order is packed in
     * @param  string  $contents  what is inside, for the courier
     * @param  Money|null  $cashOnDelivery  what the courier collects from the recipient; null when nothing
     */
    public function __construct(
        public string $reference,
        public string $recipientName,
        public string $recipientPhone,
        public ?string $recipientEmail,
        public float $weight,
        public int $packages,
        public string $contents,
        public ?Money $cashOnDelivery = null,
    ) {
        if ($weight <= 0) {
            throw new InvalidArgumentException('The weight of a shipment has to be above zero.');
        }

        if ($packages < 1) {
            throw new InvalidArgumentException('A shipment has at least one package.');
        }
    }
}
