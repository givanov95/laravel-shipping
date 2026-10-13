<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

/**
 * A street or a quarter in the directory of a courier. Not every courier gives it an id.
 */
final readonly class Street
{
    public function __construct(
        public string $name,
        public ?string $id = null,
    ) {
    }

    /**
     * @return array{id: ?string, name: string}
     */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name];
    }
}
