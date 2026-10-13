<?php

declare(strict_types=1);

namespace Givanov95\Shipping\Tests\Fixtures;

/**
 * Something a method needs built for it: the container gives it to the method.
 */
final class Warehouse
{
    public static int $built = 0;

    public string $name = 'The shop';

    public function __construct()
    {
        self::$built++;
    }
}
