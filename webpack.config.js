// webpack.config.js
const path = require('path');
const webpack = require('webpack')

const PATHS = {
    source: path.join(__dirname, 'resources/js'),
    build: path.join(__dirname, 'web')
};

const {VueLoaderPlugin} = require('vue-loader');

module.exports = (env, argv) => {
    let config = {
        production: argv.mode === 'production'
    };

    return {
        entry: [
            './resources/js/app.js'
        ],
        output: {
            path: PATHS.build,
            filename: config.production ? 'assets/inertia/js/app.min.js' : 'assets/inertia/js/app.js'
        },
        resolve: {
            extensions: ['.js', '.vue', '.json'],
            alias: {
                '@': '/' + path.resolve(__dirname, 'resources/js')
            }
        },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    use: 'vue-loader'
                },
                {
                    test: /\.css$/i,
                    use: ['style-loader', 'css-loader']
                }
            ]
        },
        plugins: [
            new VueLoaderPlugin(),
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: 'true',
                __VUE_PROD_DEVTOOLS__: 'false',
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: 'false'
            })
        ]
    };
};
