const mix = require('laravel-mix');

mix.config.publicPath = 'web';
mix.config.fileLoaderDirs.fonts = 'fonts';

mix.sass('resources/sass/app.scss', 'web/css/app.css');

mix.js('resources/js/app.js', 'web/js/app.js');
