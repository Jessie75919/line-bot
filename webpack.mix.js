let mix = require('laravel-mix');
const Clean = require('clean-webpack-plugin');

mix.webpackConfig({
    plugins: [
        new Clean([
            'public/css',
            'public/images',
            'public/js'
        ], {verbose: false})
    ],
});



//Compile Sass
mix.sass('resources/assets/sass/default.scss', 'public/css')
    .sass('resources/assets/sass/default_mobile.scss', 'public/css');


// Concat Css
mix.styles([
    'resources/assets/css/libs/fontawesome-all.min.css',
    'resources/assets/css/libs/switchery.css',
    'node_modules/dropzone/dist/dropzone.css',
    'resources/assets/css/libs/jquery.tag-editor.css',
], 'public/css/vendor.css');


// copy images & fonts to public
mix.copyDirectory('resources/assets/images', 'public/images');
mix.copyDirectory('resources/assets/css/libs/webfonts', 'public/css/webfonts');


// Combine all css
mix.combine([
    'public/css/vendor.css',
    'public/css/default.css',
    'public/css/default_mobile.css',
    'public/css/setting.css'
], 'public/css/all.css');

// minify the combining css
mix.minify('public/css/all.css').version();


mix.js('resources/assets/js/app.js', 'public/js');
mix.js('node_modules/jquery-ui/ui/core.js', 'public/js');


mix.scripts([

    'resources/assets/js/libs/jquery.caret.min.js',
    'resources/assets/js/libs/jquery.tag-editor.min.js',
    'resources/assets/js/libs/canvasjs.min.js',
    'resources/assets/js/libs/html5.min.js',
    'resources/assets/js/libs/SmoothScroll_v1.2.1.js',
    'resources/assets/js/libs/laravel.js',
    'resources/assets/js/libs/switchery.min.js',
    'resources/assets/js/custom/share.js',
    'resources/assets/js/custom/ajax.js',
    'resources/assets/js/custom/events.js',
], 'public/js/all.js').version();

//mix.js('resources/assets/js/app.js', 'public/js');