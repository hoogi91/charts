const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');

module.exports = (env, argv) => ({
    optimization: {
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
    entry: {
        "ColorPaletteInputElement": path.join(__dirname, "/Resources/Private/Assets/JavaScript/main.js"),
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
    output: {
        filename: "[name].js",
        libraryTarget: "amd",
        path: path.join(__dirname, "/Resources/Public/JavaScript"),
        publicPath: argv.mode !== "production" ? "/" : "../dist/"
    },
    externals: {
        "DocumentService": "TYPO3/CMS/Core/DocumentService",
        "Modal": "TYPO3/CMS/Backend/Modal",
    }
});
