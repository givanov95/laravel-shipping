<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

/**
 * Where a delivery ends: the door of the customer, or a point of the courier.
 */
enum DestinationType: string
{
    case Address = 'address';
    case Office = 'office';
    case Locker = 'locker';

    /**
     * An office and a locker are picked from the points of the courier.
     */
    public function isPoint(): bool
    {
        return $this !== self::Address;
    }
}
