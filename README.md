# TYPO3 Extension ``charts``

[![Version](https://img.shields.io/github/v/tag/hoogi91/charts?label=stable&style=for-the-badge)](https://packagist.org/packages/hoogi91/charts)
[![Tests](https://img.shields.io/github/actions/workflow/status/hoogi91/charts/phpunit.yml?label=tests&style=for-the-badge)](https://github.com/hoogi91/charts/actions/workflows/phpunit.yml)
[![Coverage](https://img.shields.io/codecov/c/github/hoogi91/charts?style=for-the-badge)](https://codecov.io/gh/hoogi91/charts)
[![License](https://img.shields.io/github/license/hoogi91/charts?style=for-the-badge)](https://github.com/hoogi91/charts/blob/develop/LICENSE)
[![Documentation](https://img.shields.io/github/v/tag/hoogi91/charts?color=ffe907&label=docs&style=for-the-badge)](https://docs.typo3.org/p/hoogi91/charts/2.0/en-us/)

> composer req hoogi91/charts

## Features

- Supporting editors & authors by providing
	- records to create data for charts incl. labels, data and dataset-labels
	- fluid based content elements to display charts (bar, line, pie or doughnut) in frontend
	- (optional) fill data records for charts with informations from spreadsheets when [spreadsheet](https://extensions.typo3.org/extension/spreadsheets/) extension is present in TYPO3 installation 
- Supporting administrators & developers by providing
	- select option in extension manager to manage/set the preferred chart library
	- registry to add multiple chart libraries implementing at least the `Hoogi91\Charts\DataProcessing\Charts\LibraryInterface`
	- DataProcessors to get chart data, assets and their settings from Flexform configuration

## Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an issue and connect it to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

Bugfixes: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue.

Features: Not every feature is relevant for the bulk of users. It helps to have a discussion about a new feature before you open a pull request.
