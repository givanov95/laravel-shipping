<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

/**
 * A city, town or village in the directory of a courier.
 */
final readonly class Place
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $postalCode = null,
    ) {
    }

    /**
     * @return array{id: string, name: string, postal_code: ?string}
     */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'postal_code' => $this->postalCode];
    }
}
