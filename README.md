# PHPCalendarApi

[![CI workflow](https://github.com/bjoern-hempel/php-calendar-api/actions/workflows/ci-workflow.yml/badge.svg?branch=main)](https://github.com/bjoern-hempel/php-calendar-api/actions/workflows/ci-workflow.yml)
[![Release](https://img.shields.io/github/v/release/bjoern-hempel/php-calendar-api)](https://github.com/bjoern-hempel/php-calendar-api/releases)
[![PHP](https://img.shields.io/badge/PHP-^8.0-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=flat)](https://www.php.net/supported-versions.php)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208-brightgreen.svg?style=flat)](https://phpstan.org/user-guide/rule-levels)
[![PHPCS](https://img.shields.io/badge/PHPCS-PSR12-brightgreen.svg?style=flat)](https://www.php-fig.org/psr/psr-12/)
[![LICENSE](https://img.shields.io/github/license/bjoern-hempel/php-calendar-api)](https://github.com/bjoern-hempel/php-calendar-api/blob/master/LICENSE.md)

> An API for building ready-made calendars. Includes a user area and token management. The framework is secured via tests and static code analyzers.

# Main Technologies

## Applications and Extensions

* **Symfony 6.0.x**: https://symfony.com/releases/6.0
    * **Released**: November 2021
    * **Support until**: Juli 2022
    * https://symfony.com/releases/6.0
* **API Platform 2.6.x**: https://api-platform.com/docs
    * **Released**: Jan 2021
    * https://github.com/api-platform/api-platform/releases

## Scripting language and database management systems

* **PHP 8.0.x**: https://www.php.net/releases/8.0/en.php
    * **Released**: 26 Nov 2020
    * **Active Support Until**: 26 Nov 2022
    * **Security Support Until**: 26 Nov 2023
    * https://www.php.net/supported-versions.php
* **MariaDB 10.7.1**: https://mariadb.com/kb/en/mariadb-1071-release-notes
    * **Released**: 8 November 2021
    * **Support until**: July 2026
    * https://endoflife.date/mariadb

## Helper

* **Composer 2.2.x**: https://getcomposer.org/download
* **Symfony Client**: https://symfony.com/download

## Testing and Analysis

* **PHPUnit 9.5.x**: https://phpunit.de
* **PHPStan: 1.2.x**: https://phpstan.org

## Manuals

* **Integration**: https://symfony.com/doc/current/the-fast-track/de/26-api.html

## Techniques

* **JSONLD**: https://en.wikipedia.org/wiki/JSON-LD
* **JSON**: https://en.wikipedia.org/wiki/JSON
* **REST**: https://en.wikipedia.org/wiki/Representational_state_transfer

# 1. Make it work

## 1.1 Clone project, start docker environment and install dependencies

### 1.1.1 Local development

```bash
❯ git clone git@github.com:bjoern-hempel/calendarBuilder.git && cd calendarBuilder
❯ ln -s docker-compose.dev.yml docker-compose.yml
❯ docker-compose up -d
❯ docker-compose exec php /etc/init.d/supervisor start
❯ docker-compose exec php composer install
❯ docker-compose exec php yarn install
❯ docker-compose exec php yarn encore production
❯ docker-compose exec php composer migrate-prod
❯ docker-compose exec php composer test
```