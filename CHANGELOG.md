# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Releases

### [0.19.0] - 2022-02-19

* [#54](https://github.com/bjoern-hempel/php-calendar-api/issues/54) - Add easyAdmin to API

### [0.18.0] - 2022-02-05

* [#50](https://github.com/bjoern-hempel/php-calendar-api/issues/50) - Add direct ids to response

### [0.17.0] - 2022-02-05

* [#48](https://github.com/bjoern-hempel/php-calendar-api/issues/48) - Add X-Total-Count to header

### [0.16.0] - 2022-01-29

* [#45](https://github.com/bjoern-hempel/php-calendar-api/issues/45) - Possibility to disable the JWT locally for debugging processes

### [0.15.1] - 2022-01-26

* [#43](https://github.com/bjoern-hempel/php-calendar-api/issues/43) - Add other versions to /version

### [0.15.0] - 2022-01-26

* [#41](https://github.com/bjoern-hempel/php-calendar-api/issues/41) - Add /version endpoint to API

### [0.14.1] - 2022-01-23

* [#36](https://github.com/bjoern-hempel/php-calendar-api/issues/36) - Change license comments

### [0.14.0] - 2022-01-23

* [#28](https://github.com/bjoern-hempel/php-calendar-api/issues/28) - Add API tests

### [0.13.0] - 2022-01-20

* [#36](https://github.com/bjoern-hempel/php-calendar-api/issues/36) - Add security features to API

### [0.12.0] - 2022-01-18

* [#3](https://github.com/bjoern-hempel/php-calendar-api/issues/3) - Add JWT refresh token bundle

### [0.11.0] - 2022-01-17

* [#2](https://github.com/bjoern-hempel/php-calendar-api/issues/2) - Add JWT Authentication

### [0.10.0] - 2022-01-16

* [#32](https://github.com/bjoern-hempel/php-calendar-api/issues/32) - Add https://github.com/ixnode/bash-version-manager to repo

### [0.9.0] - 2022-01-15

* [#29](https://github.com/bjoern-hempel/php-calendar-api/issues/29) - Add app version to API platform

### [0.8.1] - 2022-01-15

* Fix documentation CHANGELOG.md

### [0.8.0] - 2022-01-15

* [#1](https://github.com/bjoern-hempel/php-calendar-api/issues/1) - Add API Platform
* [#19](https://github.com/bjoern-hempel/php-calendar-api/issues/19) - Add simple calendar page build test

### [0.7.0] - 2022-01-06

* [#21](https://github.com/bjoern-hempel/php-calendar-api/issues/21) - Build qr code dynamically

### [0.6.0] - 2022-01-04

* [#23](https://github.com/bjoern-hempel/php-calendar-api/issues/23) - Add workflow badge

### [0.5.0] - 2022-01-04

* [#6](https://github.com/bjoern-hempel/php-calendar-api/issues/6) - Connect source and destination images with user folder

### [0.4.0] - 2022-01-04

* [#7](https://github.com/bjoern-hempel/php-calendar-api/issues/7) - Add CI Pipeline

### [0.3.0] - 2022-01-04

* [#17](https://github.com/bjoern-hempel/php-calendar-api/issues/17) - Add PHP-CS-Fixer

### [0.2.0] - 2022-01-03

* [#4](https://github.com/bjoern-hempel/php-calendar-api/issues/4) - Add badges to README.md

### [0.1.3] - 2022-01-03

* [#13](https://github.com/bjoern-hempel/php-calendar-api/issues/13) - Rename project from calendarBuilder to php-calendar-api (including docker)

### [0.1.2] - 2022-01-03

* [#13](https://github.com/bjoern-hempel/php-calendar-api/issues/13) - Rename project from calendarBuilder to php-calendar-api

### [0.1.1] - 2022-01-03

* Add README.md

### [0.1.0] - 2022-01-03

* Initial release
* Add PHPStan with the highest level
* Add PHPUnit
* Add unit tests
* Add create page command
* Add create calendar command
* Add first db structure
* Add data fixtures
* Add calendar builder service
* Add calendar loader service
* Add holiday group loader service

## Add new version

```bash
# Checkout master branch
❯ git checkout main && git pull

# Check current version
❯ bin/version-manager --current

# Add new version to .evn and VERSION file (increase patch version)
❯ bin/version-manager --patch

# Change changelog
❯ vi CHANGELOG.md

# Push new version
❯ git add CHANGELOG.md VERSION .env && git commit -m "Add version $(cat VERSION)" && git push

# Tag and push new version
❯ git tag -a "$(cat VERSION)" -m "Version $(cat VERSION)" && git push origin "$(cat VERSION)"
```
