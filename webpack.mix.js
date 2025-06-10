const mix = require('laravel-mix');
const exec = require('child_process').exec;
require('dotenv').config();

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

// const glob = require('glob'); // Commented out
const path = require('path'); // Keep path for now, might be used by other parts or future needs

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

// function mixAssetsDir(query, cb) { // Commented out
//   (glob.sync('resources/' + query) || []).forEach(f => {
//     f = f.replace(/[\/]+/g, '/');
//     cb(f, f.replace('resources', 'public'));
//   });
// }

// const sassOptions = { // Commented out
//   precision: 5
// };

// // plugins Core stylesheets
// mixAssetsDir('sass/plugins/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\ਤੀsass(\ਤੀ)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// // themes Core stylesheets
// mixAssetsDir('sass/themes/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\ਤੀsass(\ਤੀ)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// // pages Core stylesheets
// mixAssetsDir('sass/pages/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\ਤੀsass(\ਤੀ)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// // Core stylesheets
// mixAssetsDir('sass/core/**/!(_)*.scss', (src, dest) => mix.sass(src, dest.replace(/(\ਤੀsass(\ਤੀ)/, '$1css$2').replace(/\.scss$/, '.css'), sassOptions));

// // script js
// mixAssetsDir('js/scripts/**/*.js', (src, dest) => mix.scripts(src, dest));

/*
 |--------------------------------------------------------------------------
 | Application assets
 |--------------------------------------------------------------------------
 */

// mixAssetsDir('vendors/js/**/*.js', (src, dest) => mix.scripts(src, dest)); // Commented out
// mixAssetsDir('vendors/css/**/*.css', (src, dest) => mix.copy(src, dest)); // Commented out
// mixAssetsDir('vendors/css/editors/quill/fonts/', (src, dest) => mix.copy(src, dest)); // Commented out
// mix.copyDirectory('resources/images', 'public/images'); // Commented out
// mix.copyDirectory('resources/fonts', 'public/fonts'); // Commented out



mix.js('resources/js/core/app-menu.js', 'public/js/core')
  .js('resources/js/core/app.js', 'public/js/core')
  .sass('resources/sass/bootstrap.scss', 'public/css')
  .sass('resources/sass/bootstrap-extended.scss', 'public/css')
  .sass('resources/sass/colors.scss', 'public/css')
  .sass('resources/sass/components.scss', 'public/css')
  .sass('resources/sass/custom-rtl.scss', 'public/css')
  .sass('resources/sass/custom-laravel.scss', 'public/css')
  .postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
  ]);

// mix.then(() => { // Commented out
//   if (process.env.MIX_CONTENT_DIRECTION === "rtl") {
//     let command = `node ${path.resolve('node_modules/rtlcss/bin/rtlcss.js')} -d -e ".css" ./public/css/ ./public/css/`;
//     exec(command, function (err, stdout, stderr) {
//       if (err !== null) {
//         console.log(err);
//       }
//     });
//     // exec('./node_modules/rtlcss/bin/rtlcss.js -d -e ".css" ./public/css/ ./public/css/');
//   }
// });


// if (mix.inProduction()) {
//   mix.version();
//   mix.webpackConfig({
//     output: {
//       publicPath: '/demo/vuexy-bootstrap-laravel-admin-template/demo-1/'
//     }
//   });
//   mix.setResourceRoot("/demo/vuexy-bootstrap-laravel-admin-template/demo-1/");
// }
