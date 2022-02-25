const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');

const config = {
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
                use: [
                    "babel-loader",
                    "eslint-loader",
                ],
            },
            {
                test: /\.(css)$/,
                use: ["css-loader"],
            },
        ]
    },
};

module.exports = [
    Object.assign({}, config, {
        name: "ColorPaletteInputElement",
        entry: path.join(__dirname, "/Resources/Private/Assets/JavaScript/main.js"),
        output: {
            filename: "ColorPaletteInputElement.js",
            library: {type: 'amd-require'},
            path: path.join(__dirname, "/Resources/Public/JavaScript"),
            publicPath: "/",
        },
        externals: {
            "DocumentService": "TYPO3/CMS/Core/DocumentService",
            "Modal": "TYPO3/CMS/Backend/Modal",
        },
    }),

    Object.assign({}, config, {
        entry: {
            apexcharts: path.join(__dirname, "/Resources/Private/Assets/JavaScript/Libs/apexcharts.js"),
            chartjs: path.join(__dirname, "/Resources/Private/Assets/JavaScript/Libs/chartjs.js"),
        },
        output: {
            filename: "[name].js",
            library: {
                name: 'Hoogi91.Charts',
                type: 'window',
                export: 'default',
            },
            path: path.join(__dirname, "/Resources/Public/JavaScript"),
            publicPath: "/",
        },
    })
];
