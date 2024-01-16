<?php
/**
 * A graphql-type for address-data coming from the address-field
 *
 * @author David Faber
 * @copyright (c) Peritus Webdesign GmbH
 */

namespace Rias\StatamicAddressField\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

/**
 * A graphql-type for address-data coming from the address-field
 *
 * @author David Faber
 * @copyright (c) Peritus Webdesign GmbH
 */
class AddressDataType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AddressData',
        'description' => 'address-data coming from the address-field',
    ];

    public function fields(): array
    {
        return [
            'name' => ['type' => Type::string()],
            'street' => ['type' => Type::string()],
            'postCode' => ['type' => Type::string()],
            'city' => ['type' => Type::string()],
            'country' => ['type' => Type::string()],
            'latitude' => ['type' => Type::string()],
            'longitude' => ['type' => Type::string()],
        ];
    }
}
