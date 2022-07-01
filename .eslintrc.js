module.exports = {
    root: true,
    env: {
        browser: true,
        node: true,
        es6: true
    },
    parser: "@babel/eslint-parser",
    parserOptions: {
        sourceType: "module"
    },
    extends: [
        "eslint:recommended"
    ],
    rules: {
        "no-empty": [2, {"allowEmptyCatch": true}],
        "no-unused-vars": [1, {"vars": "all", "args": "after-used", "ignoreRestSiblings": false}],
        "indent": [2, 4],
        "semi": [2, "always"],
    },
    globals: {
        "ApexCharts": "readonly",
        "Chart": "readonly",
    }
};
