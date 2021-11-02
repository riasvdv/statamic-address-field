<?php

namespace Rias\StatamicAddressField\Tags;

use Rias\StatamicAddressField\AddressData;
use Rias\StatamicAddressField\Countries;
use Statamic\Tags\Tags;

class Address extends Tags
{
    public function countries(): array
    {
        return Countries::toArray();
    }

    public function staticMap(): ?string
    {
        $image = $this->staticMapUrl();

        if (! $image) {
            return null;
        }

        return "<img src='{$image}' alt=''>";
    }

    public function map(): ?string
    {
        $key = config('statamic.address-field.google_maps_api_key');

        if (! $key) {
            return null;
        }

        $data = AddressData::from($this->params->get('address', []));

        $url = "https://www.google.com/maps/embed/v1/{$this->params->get('type', 'place')}?key={$key}";

        $params = $this->params->except(['address', 'type', 'width', 'height'])->toArray();

        if ($this->params->get('type') === 'directions') {
            $params['origin'] ??= 'current+location';
            $params['destination'] ??= $data->toString();
        } else {
            $params['q'] = $data->toString();
        }

        $params['zoom'] ??= 14;

        $query = http_build_query($params);
        $url .= "&" . $query;

        return <<<HTML
            <iframe
              width="{$this->params->get('width', 640)}"
              height="{$this->params->get('height', 640)}"
              style="border:0"
              loading="lazy"
              src="{$url}">
            </iframe>
        HTML;
    }

    public function staticMapUrl(): ?string
    {
        $key = config('statamic.address-field.google_maps_api_key');

        if (! $key) {
            return null;
        }

        $data = AddressData::from($this->params->get('address', []));

        $zoom = $this->params->get('zoom', 14);
        $width = $this->params->get('width', '640');
        $height = $this->params->get('height', '640');
        $style = $this->params->get('style');
        $color = $this->params->get('color');
        $icon = $this->params->get('icon');
        $scale = $this->params->get('scale', 1);

        // Support a custom marker color for each tag or fall back to the color set in settings, or the default color
        if ($color && preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            $markerColor = '0x' . ltrim($color, '#');
        } else {
            $markerColor = config('statamic.address-field.default_marker_color')
                ? '0x' . ltrim(config('statamic.address-field.default_marker_color'), '#')
                : 'red';
        }

        // Check if the tag has a custom marker set or if we have a default custom maker
        $markerIcon = $icon ?? config('statamic.address-field.default_marker_icon');

        // Use the custom marker if we have it, otherwise fall back to the standard marker + possible custom colors
        $markerProperties = $markerIcon
            ? "icon:" . $markerIcon. '|'
            : "color:" . $markerColor . '|';

        $baseLink = 'https://maps.googleapis.com/maps/api/staticmap?';

        $style = config("statamic.address-field.map_styles.{$style}");
        if (! $style) {
            $default = config('statamic.address-field.default_map_style');
            $style = config("statamic.address-field.map_styles.{$default}");
        }

        $params = [
            'zoom' => $zoom,
            'size' => "{$width}x{$height}",
            'scale' => $scale,
            'maptype' => 'roadmap',
            'key' => $key,
            'style' => $style,
        ];

        $location = '';

        $lat = $data->latitude;
        $lng = $data->longitude;
        $location .= '&markers=' . $markerProperties. $lat . ',' . $lng;

        $image = $baseLink . http_build_query($params) . $location;

        return urldecode($image);
    }

    public function directions(): string
    {
        $data = AddressData::from($this->params->get('address', []));
        $currentLocation = $this->params->bool('currentLocation', true);

        $str = $currentLocation
            ? 'https://www.google.com/maps/dir/current+location/'
            : 'https://www.google.com/maps/dir//';

        return $str . urlencode($data->toString());
    }
}
