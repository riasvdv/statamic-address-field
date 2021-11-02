<?php

namespace Rias\StatamicAddressField\Modifiers;

use Exception;
use Rias\StatamicAddressField\Countries;
use Statamic\Modifiers\Modifier;

class CountryName extends Modifier
{
    public function index($value): ?string
    {
        try {
            return Countries::getCountryNameByAlpha2($value, app()->getLocale());
        } catch (Exception $e) {
            return null;
        }
    }
}
