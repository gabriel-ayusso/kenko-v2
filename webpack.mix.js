const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);
mix.js('resources/js/app.js', 'public/js').react();
mix.js('resources/js/components/booking/App.jsx', 'public/js/app-booking.js').react()
mix.js('resources/js/employee-booking.js', 'public/js/employee-booking.js')
   .js('resources/js/guest-booking.js', 'public/js/guest-booking.js')
   .sass('resources/sass/app.scss', 'public/css');
