# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Releases

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
# checkout master branch
$ git checkout main && git pull

# add new version
$ echo "0.1.1" > VERSION

# Change changelog
$ vi CHANGELOG.md

# Push new version
$ git add CHANGELOG.md VERSION && git commit -m "Add version $(cat VERSION)" && git push

# Tag and push new version
$ git tag -a "$(cat VERSION)" -m "Version $(cat VERSION)" && git push origin "$(cat VERSION)"
```
