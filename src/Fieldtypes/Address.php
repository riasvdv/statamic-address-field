<?php

namespace Rias\StatamicAddressField\Fieldtypes;

use Rias\StatamicAddressField\AddressData;
use Rias\StatamicAddressField\Countries;
use Statamic\Facades\GraphQL;
use Statamic\Fields\Fieldtype;


class Address extends Fieldtype
{
    protected $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" height="24" width="24"><g transform="matrix(2,0,0,2,0,0)"><path d="M23.25,9V4.65a1.5,1.5,0,0,0-.943-1.393l-6-2.4a1.5,1.5,0,0,0-1.114,0L8.807,3.412a1.5,1.5,0,0,1-1.114,0L1.779,1.046a.75.75,0,0,0-1.029.7V16.119a1.5,1.5,0,0,0,.943,1.393l6,2.4a1.5,1.5,0,0,0,1.114,0l2.881-1.153" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M8.25 3.519L8.25 20.019" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M15.75 0.75L15.75 8.25" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.75,15.449a.375.375,0,0,1,.375.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.375,15.824a.375.375,0,0,1,.375-.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.75,16.2a.375.375,0,0,1-.375-.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M19.125,15.824a.375.375,0,0,1-.375.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.75,11.324a4.5,4.5,0,0,1,4.5,4.5c0,1.921-2.688,5.576-3.909,7.138a.75.75,0,0,1-1.182,0c-1.221-1.561-3.909-5.217-3.909-7.138A4.5,4.5,0,0,1,18.75,11.324Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></g></svg>';

    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    public function preProcessIndex($data)
    {
        $address = AddressData::from($data);

        return str_replace("\n", ", ", $address->format());
    }

    public function preload()
    {
        return [
            'countries' => collect(Countries::toArray())->map(function ($country) {
                return ['value' => $country['alpha2'], 'label' => $country['display']];
            })->toArray(),
        ];
    }

    public function configFieldItems(): array
    {
        return [
            'enabledFields' => [
                'display' => __('Enabled fields'),
                'type' => 'select',
                'options' => [
                    'name' => __('Name'),
                    'street' => __('Street'),
                    'street2' => __('Street 2'),
                    'postCode' => __('Postcode'),
                    'city' => __('City'),
                    'state' => __('State'),
                    'country' => __('Country'),
                    'latitude' => __('Latitude'),
                    'longitude' => __('Longitude'),
                ],
                'multiple' => true,
                'default' => [
                    'name',
                    'street',
                    'street2',
                    'postCode',
                    'city',
                    'state',
                    'country',
                    'latitude',
                    'longitude',
                ],
            ],
            'defaultCountry' => [
                'display' => __('Default Country'),
                'type' => 'select',
                'options' => collect(Countries::toArray())->mapWithKeys(function ($country) {
                    return [$country['alpha2'] => $country['display']];
                }),
            ],

            'geoCode' => [
                'display' => __('Get coordinates for address'),
                'type' => 'toggle',
                'default' => true,
                'width' => 50,
            ],

            'showCoordinates' => [
                'display' => __('Show coordinates'),
                'type' => 'toggle',
                'default' => false,
                'width' => 50,
            ],
        ];
    }

    public function toGqlType()
    {
        return GraphQL::type('AddressData');
    }
}
