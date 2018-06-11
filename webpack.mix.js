let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/directory.ts', 'public/js')
    .js('resources/assets/js/share.ts', 'public/js')
    .js('resources/assets/js/notify.ts', 'public/js')
    .js('resources/assets/js/devices.ts', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .autoload({
        jquery: ['$', 'global.jQuery', "jQuery", "global.$", "jquery", "global.jquery"]
    });
