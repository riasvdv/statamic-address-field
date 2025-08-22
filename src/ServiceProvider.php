<?php

namespace Rias\StatamicAddressField;

use Rias\StatamicAddressField\GraphQL\Types\AddressDataType;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\GraphQL;

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

    protected $vite = [
        'input' => [
            'resources/js/cp.js',
        ],
        'publicDirectory' => 'dist',
        'hotFile' => __DIR__.'/../dist/hot',
    ];

    protected $publishables = [
        __DIR__ . '/../dist/images' => 'images',
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/address-field.php', 'statamic.address-field');

        $this->publishes([
            __DIR__.'/../config/address-field.php' => config_path('statamic/address-field.php'),
        ], 'statamic-address-field-config');
    }

    public function bootAddon()
    {
        if (config('statamic.graphql.enabled')) {
            GraphQL::addType(AddressDataType::class);
        }
    }
}
