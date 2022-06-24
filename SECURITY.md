# Security Policy

## Supported Versions

| Charts       | TYPO3       | PHP       | Supported                     |
| ------------ | ----------- |-----------|-------------------------------|
| dev-master   | 10.4 - 11.5 | 8.1       | :white_check_mark: (unstable) |
| 2.x          | 10.4 - 11.5 | 7.4 - 8.1 | :white_check_mark:            |
| 1.x          | 8.7 - 10.4  | 7.0 - 7.4 | :x:                           |

### Release Management

This extension uses [**semantic versioning**][1], which means, that
* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes wich can be refactorings, features or bugfixes.

## Reporting a Vulnerability

Please write me an email (see [profile](https://github.com/hoogi91))
if you found a vulnerability which is not related to a dependency and/or is already a published CVE.

[1]: https://semver.org/
