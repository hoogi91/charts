# TYPO3 Extension ``charts``

[![CI](https://github.com/hoogi91/charts/workflows/CI/badge.svg?event=push)](https://github.com/hoogi91/charts/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/hoogi91/charts/branch/develop/graph/badge.svg)](https://codecov.io/gh/hoogi91/charts)
[![License](https://poser.pugx.org/hoogi91/charts/license)](https://packagist.org/packages/hoogi91/charts)

## Features

- Supporting editors & authors by providing
	- records to create data for charts incl. labels, data and dataset-labels
	- fluid based content elements to display charts (bar, line, pie or doughnut) in frontend
	- (optional) fill data records for charts with informations from spreadsheets when [spreadsheet](https://extensions.typo3.org/extension/spreadsheets/) extension is present in TYPO3 installation 
- Supporting administrators & developers by providing
	- select option in extension manager to manage/set the preferred chart library
	- registry to add multiple chart libraries implementing at least the `Hoogi91\Charts\DataProcessing\Charts\LibraryInterface`
	- DataProcessors to get chart data, assets and their settings from Flexform configuration
* [Documentation][1]

## Usage

### Installation

#### Installation using Composer

The recommended way to install the extension is using [Composer][2].

Run the following command within your Composer based TYPO3 project:

```
composer req hoogi91/charts
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the [extension][3] with the extension manager module.

## Administration corner

### Versions and support

| Charts       | TYPO3       | PHP       | Support / Development                   |
| ------------ | ----------- |-----------|---------------------------------------- |
| dev-master   | 10.4 - 11.5 | 8.1       | unstable development branch             |
| 2.x          | 10.4 - 11.5 | 7.4 - 8.1 | features, bugfixes, security updates    |
| 1.x          | 8.7 - 10.4  | 7.0 - 7.4 | none                                    |

### Release Management

This extension uses [**semantic versioning**][4], which means, that
* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes wich can be refactorings, features or bugfixes.

### Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an issue and connect it to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

Bugfixes: Please describe what kind of bug your fix solve and give us feedback how to reproduce the issue.

Features: Not every feature is relevant for the bulk of users. It helps to have a discussion about a new feature before you open a pull request.

[1]: https://docs.typo3.org/p/hoogi91/charts/master/en-us/
[2]: https://getcomposer.org/
[3]: https://extensions.typo3.org/extension/charts
[4]: https://semver.org/
