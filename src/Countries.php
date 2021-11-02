<?php

namespace Rias\StatamicAddressField;

use Exception;
use Giggsey\Locale\Locale;
use League\ISO3166\ISO3166;

class Countries
{
    public static function toArray()
    {
        return collect((new ISO3166())->all())
            ->map(function ($country) {
                $display = Locale::getDisplayRegion('-' . $country['alpha2'], app()->getLocale());

                $country['display'] = $display ?: $country['name'];

                return $country;
            })
            ->sortBy('alpha2')
            ->toArray();
    }

    public static function getCountryNameByAlpha2($code, $locale = null): string
    {
        $data = new ISO3166();
        $country = $data->alpha2($code);
        $locale ??= app()->getLocale();

        try {
            return Locale::getDisplayRegion('-' . $country['alpha2'], $locale);
        } catch (Exception $e) {
            return $country['name'];
        }
    }
}
