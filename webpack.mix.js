const mix = require("laravel-mix");
const exec = require("child_process").exec;
require("dotenv").config();

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

const glob = require("glob");
const path = require("path");

/*
 |--------------------------------------------------------------------------
 | Vendor assets
 |--------------------------------------------------------------------------
 */

function mixAssetsDir(query, cb) {
    (glob.sync("resources/" + query) || []).forEach((f) => {
        f = f.replace(/[\\\/]+/g, "/");
        cb(f, f.replace("resources", "public"));
    });
}

const sassOptions = {
    precision: 5,
};

// plugins Core stylesheets
mixAssetsDir("sass/plugins/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest
            .replace(/(\\|\/)sass(\\|\/)/, "$1css$2")
            .replace(/\.scss$/, ".css"),
        sassOptions
    )
);

// themes Core stylesheets
mixAssetsDir("sass/themes/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest
            .replace(/(\\|\/)sass(\\|\/)/, "$1css$2")
            .replace(/\.scss$/, ".css"),
        sassOptions
    )
);

// pages Core stylesheets
mixAssetsDir("sass/pages/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest
            .replace(/(\\|\/)sass(\\|\/)/, "$1css$2")
            .replace(/\.scss$/, ".css"),
        sassOptions
    )
);

// Core stylesheets
mixAssetsDir("sass/core/**/!(_)*.scss", (src, dest) =>
    mix.sass(
        src,
        dest
            .replace(/(\\|\/)sass(\\|\/)/, "$1css$2")
            .replace(/\.scss$/, ".css"),
        sassOptions
    )
);

// script js
mixAssetsDir("js/scripts/**/*.js", (src, dest) => mix.scripts(src, dest));

/*
 |--------------------------------------------------------------------------
 | Application assets
 |--------------------------------------------------------------------------
 */

mixAssetsDir("vendors/js/**/*.js", (src, dest) => mix.scripts(src, dest));
mixAssetsDir("vendors/css/**/*.css", (src, dest) => mix.copy(src, dest));
mixAssetsDir("vendors/css/editors/quill/fonts/", (src, dest) =>
    mix.copy(src, dest)
);
// mix.copyDirectory("resources/images", "public/images");
// mix.copyDirectory("resources/fonts", "public/fonts");

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       // Để trống ở đây, Laravel Mix sẽ tự động đọc file postcss.config.js
   ]);

mix.then(() => {
    if (process.env.MIX_CONTENT_DIRECTION === "rtl") {
        let command = `node ${path.resolve(
            "node_modules/rtlcss/bin/rtlcss.js"
        )} -d -e ".css" ./public/css/ ./public/css/`;
        exec(command, function (err, stdout, stderr) {
            if (err !== null) {
                console.log(err);
            }
        });
        // exec('./node_modules/rtlcss/bin/rtlcss.js -d -e ".css" ./public/css/ ./public/css/');
    }
});

// if (mix.inProduction()) {
//   mix.version();
//   mix.webpackConfig({
//     output: {
//       publicPath: '/demo/vuexy-bootstrap-laravel-admin-template/demo-1/'
//     }
//   });
//   mix.setResourceRoot("/demo/vuexy-bootstrap-laravel-admin-template/demo-1/");
// }

mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules\/(?!(laravel-echo|pusher-js)\/).*/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: [
                            [
                                "@babel/preset-env",
                                {
                                    targets: {
                                        browsers: [
                                            ">0.25%",
                                            "not ie 11",
                                            "not op_mini all",
                                        ],
                                    },
                                    useBuiltIns: "usage",
                                    corejs: 3,
                                },
                            ],
                        ],
                        plugins: [
                            "@babel/plugin-transform-nullish-coalescing-operator",
                            "@babel/plugin-transform-optional-chaining",
                            "@babel/plugin-proposal-object-rest-spread",
                        ],
                    },
                },
            },
        ],
    },
});
