<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Contracts;

use Givanov95\Shipping\Destination;

/**
 * For a method that says where it delivers. A method without it delivers in the country of
 * the shop, to a parcel in any currency, and the free delivery rule of the shop holds for it.
 */
interface DeliveryCountries
{
    /**
     * The countries the method delivers to for a parcel in the given currency, as ISO 3166-1
     * alpha-2 codes. None means the method is not offered for that parcel: the shop has no
     * exchange rate, so a method with no price in the currency has nothing to charge.
     *
     * @return list<string>
     */
    public function countries(string $currency): array;

    /**
     * Whether a parcel above the free delivery threshold of the shop is delivered there for free.
     */
    public function freeDeliveryApplies(Destination $destination): bool;
}
