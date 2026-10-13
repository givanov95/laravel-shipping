<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

/**
 * An office or a locker of a courier.
 */
final readonly class Point
{
    public function __construct(
        public string $code,
        public string $name,
    ) {
    }

    /**
     * @return array{code: string, name: string}
     */
    public function toArray(): array
    {
        return ['code' => $this->code, 'name' => $this->name];
    }
}
