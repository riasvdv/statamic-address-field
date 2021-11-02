<?php

namespace Rias\StatamicAddressField;

class AddressData
{
    public ?string $name;

    public ?string $street;

    public ?string $street2;

    public ?string $postCode;

    public ?string $city;

    public ?string $state;

    public ?string $country;

    public ?float $latitude;

    public ?float $longitude;

    public function __construct(
        ?string $name = null,
        ?string $street = null,
        ?string $street2 = null,
        ?string $postCode = null,
        ?string $city = null,
        ?string $state = null,
        ?string $country = null,
        ?float $latitude = null,
        ?float $longitude = null
    ) {
        $this->name = $name;
        $this->street = $street;
        $this->street2 = $street2;
        $this->postCode = $postCode;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function from(?array $data): self
    {
        if (! $data) {
            return new self();
        }

        return new self(
            $data['name'] ?? null,
            $data['street'] ?? null,
            $data['street2'] ?? null,
            $data['postCode'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['country'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
        );
    }

    public function toString(string $glue = '+'): string
    {
        $data = [];
        $data['street'] = $this->street;
        $data['street2'] = $this->street2;
        $data['postCode'] = $this->postCode;
        $data['city'] = $this->city;
        $data['state'] = $this->state;
        $data['country'] = $this->countryName();

        return implode($glue, array_filter($data));
    }

    public function format(): string
    {
        $formatted = "{$this->street}";

        if ($this->street2) {
            $formatted .= "\n{$this->street2}";
        }

        if ($this->postCode) {
            $formatted .= "\n{$this->postCode}";
        }

        if ($this->city) {
            $formatted .= $this->postCode
                ? " {$this->city}"
                : "\n{$this->city}";
        }

        if ($this->state) {
            $formatted .= "\n{$this->state}";
        }

        if ($this->country) {
            $country = Countries::getCountryNameByAlpha2($this->country);
            $formatted .= $this->state
                ? " {$country}"
                : "\n{$country}";
        }

        return $formatted;
    }

    public function countryName(string $locale = null): ?string
    {
        return $this->country
            ? Countries::getCountryNameByAlpha2($this->country, $locale ?? app()->getLocale())
            : null;
    }
}
