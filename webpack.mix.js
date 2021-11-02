let mix = require('laravel-mix');

mix.js('resources/js/addon.js', 'dist/js').vue({
    options: {
        transformAssetUrls: false,
    }
});
mix.copyDirectory('node_modules/leaflet/dist/images', 'dist/images');
