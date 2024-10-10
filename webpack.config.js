const ESLintPlugin = require("eslint-webpack-plugin");
const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');

const config = {
    plugins: [new ESLintPlugin()],
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                parallel: true,
                terserOptions: {
                    output: {
                        comments: false,
                    },
                },
                extractComments: false,
            }),
        ],
    },
    module: {
        rules: [
            {
                test: /\.(js)$/,
                exclude: /node_modules/,
                use: ["babel-loader"],
            },
            {
                test: /\.(css)$/,
                use: ["css-loader"],
            },
        ],
    },
};

module.exports = [
    // Other configurations...

    // Configuration for chartjs
    Object.assign({}, config, {
        entry: {
            chartjs: path.join(__dirname, "/Resources/Private/Assets/JavaScript/Libs/chartjs.js"),
        },
        output: {
            filename: "[name].js",
            library: {
                name: 'Hoogi91.Charts',
                type: 'umd',
            },
            path: path.join(__dirname, "/Resources/Public/JavaScript"),
            publicPath: "/",
        },
        // Adjust externals if needed
        externals: [
            function ({ request }, callback) {
                // Exclude all imports that start with "@typo3/"
                if (request.startsWith('@typo3/')) {
                    return callback(null, `module ${request}`);
                }
                callback();
            },
        ],
    }),
];

