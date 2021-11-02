<?php

namespace Rias\StatamicAddressField;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $modifiers = [
        Modifiers\CountryName::class,
    ];

    protected $tags = [
        Tags\Address::class,
    ];

    protected $fieldtypes = [
        Fieldtypes\Address::class,
    ];

    protected $scripts = [
        __DIR__ . '/../dist/js/addon.js',
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/address-field.php', 'statamic.address-field');

        $this->publishes([
            __DIR__.'/../config/address-field.php' => config_path('statamic/address-field.php'),
        ], 'statamic-address-field-config');
    }
}
