import globals from "globals";
import babelParser from "@babel/eslint-parser";
import js from "@eslint/js";

export default [
    js.configs.recommended,
    {
        files: ["**/*.js"],
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
                "ApexCharts": "readonly",
                "Chart": "readonly",
            },
            parser: babelParser,
            ecmaVersion: "latest",
            sourceType: "module",
        },
        rules: {
            "no-empty": [2, {"allowEmptyCatch": true}],
            "no-unused-vars": [1, {"vars": "all", "args": "after-used", "ignoreRestSiblings": false, "caughtErrors": "none"}],
            "indent": [2, 4],
            "semi": [2, "always"],
        }
    }
];
