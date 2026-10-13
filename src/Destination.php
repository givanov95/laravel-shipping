<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

/**
 * The choice of the customer in the shape every delivery method gives back, so the shop can
 * store and show it without knowing the method. `courierData` is the exception: only the
 * method that wrote it reads it (the id of a city, of a street).
 */
final readonly class Destination
{
    /**
     * @param  array<string, mixed>  $courierData
     */
    public function __construct(
        public string $method,
        public DestinationType $type,
        public string $countryCode,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $street = null,
        public ?string $streetNumber = null,
        public ?string $note = null,
        public ?string $pointCode = null,
        public ?string $pointName = null,
        public array $courierData = [],
    ) {
    }

    /**
     * The fields in snake_case, as a shop stores them: one column each, `courier_data` as JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'method'        => $this->method,
            'type'          => $this->type->value,
            'country_code'  => $this->countryCode,
            'city'          => $this->city,
            'postal_code'   => $this->postalCode,
            'street'        => $this->street,
            'street_number' => $this->streetNumber,
            'note'          => $this->note,
            'point_code'    => $this->pointCode,
            'point_name'    => $this->pointName,
            'courier_data'  => $this->courierData === [] ? null : $this->courierData,
        ];
    }
}
