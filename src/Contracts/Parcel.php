<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Contracts;

/**
 * What is delivered, as far as a method has to know it to name a price. The cart of the shop
 * implements it.
 */
interface Parcel
{
    /**
     * The ISO 4217 code of the currency the customer pays in.
     */
    public function getCurrency(): string;
}
