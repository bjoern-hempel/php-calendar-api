# PHPCalendarApi

[![CI workflow](https://github.com/bjoern-hempel/php-calendar-api/actions/workflows/ci-workflow.yml/badge.svg?branch=main)](https://github.com/bjoern-hempel/php-calendar-api/actions/workflows/ci-workflow.yml)
[![Release](https://img.shields.io/github/v/release/bjoern-hempel/php-calendar-api)](https://github.com/bjoern-hempel/php-calendar-api/releases)
[![PHP](https://img.shields.io/badge/PHP-^8.0-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=flat)](https://www.php.net/supported-versions.php)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208-brightgreen.svg?style=flat)](https://phpstan.org/user-guide/rule-levels)
[![PHPCS](https://img.shields.io/badge/PHPCS-PSR12-brightgreen.svg?style=flat)](https://www.php-fig.org/psr/psr-12/)
[![LICENSE](https://img.shields.io/github/license/bjoern-hempel/php-calendar-api)](https://github.com/bjoern-hempel/php-calendar-api/blob/master/LICENSE.md)

> An API for building ready-made calendars. Includes a user area and token management. The framework is secured via tests and static code analyzers.

# 0. Main Technologies

## 0.1 Applications and Extensions

* **Symfony 6.1.x**: https://symfony.com/releases/6.1
    * **Released**: May 2022
    * **Support until**: January 2023
    * https://symfony.com/releases/6.1
    * https://endoflife.date/symfony
* **API Platform 3.0.x**: https://api-platform.com/docs
    * **Released**: 15 September 2022
    * https://github.com/api-platform/api-platform/releases
    * https://endoflife.date/api-platform

## 0.2 Scripting language and database management systems

* **PHP 8.1.x**: https://www.php.net/releases/8.1/en.php
    * **Released**: 23 November 2021
    * **Active Support Until**: 25 November 2023
    * **Security Support Until**: 25 November 2024
    * https://www.php.net/supported-versions.php
    * https://endoflife.date/php
* **MariaDB 10.7.3**: https://mariadb.com/kb/en/mariadb-1073-release-notes/
    * **Released**: 8 February 2022
    * **Support until**: 14 February 2023
    * https://endoflife.date/mariadb

## 0.3 Helper

* **Composer 2.4.x**: https://getcomposer.org/download
* **Symfony Client**: https://symfony.com/download

## 0.4 Testing and Analysis

* **PHPUnit 9.5.x**: https://phpunit.de
* **PHPStan: 1.9.x**: https://phpstan.org
* **PHP Magic Number Detector (PHPMND): 3.0.x**: https://github.com/povils/phpmnd
* **Rector PHP: 0.14.x**: https://github.com/rectorphp/rector

## 0.5 Manuals

* **Integration**: https://symfony.com/doc/current/the-fast-track/de/26-api.html

## 0.6 Techniques

* **JSONLD**: https://en.wikipedia.org/wiki/JSON-LD
* **JSON**: https://en.wikipedia.org/wiki/JSON
* **REST**: https://en.wikipedia.org/wiki/Representational_state_transfer

# 1. Installation

```bash
git clone git@github.com:bjoern-hempel/calendarBuilder.git && cd calendarBuilder
```

## 1.1 Local development

The development of the unit classes, the unit tests and the general framework can be done locally.

### 1.1.1 Check PHP version

At least version 8.1 is required.

```bash
php -v
```

```bash
PHP 8.1.12 (cli) (built: Oct 28 2022 18:35:51) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.12, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.12, Copyright (c), by Zend Technologies
    with Xdebug v3.1.5, Copyright (c) 2002-2022, by Derick Rethans
```

### 1.1.2 Composer install

```bash
composer install
```

### 1.1.3 Show version

```bash
bin/console version:show
```

```bash
  Version:         0.33.0
  License:         Copyright (c) 2022 Björn Hempel
  Authors:         Björn Hempel <bjoern@hempel.li>
  PHP Version:     8.1.12
  Symfony Version: 6.1.7
```

### 1.1.4 Reinitialize the test database

Required for functional tests:

```bash
bin/console db:reinitialize --env=test
```

or

```bash
composer db:initialize:test
```

Check db size:

```bash
du -h var/cache/test/test.db
```

```text
232K    var/cache/test/test.db
```

### 1.1.5 Run first test

```bash
composer test
```

## 1.2 Execution with docker

If there is no local PHP version 8.1, or you want to develop the API with real data and a database, the Docker LAMP
the integrated Docker LAMP stack can be used. This procedure is the recommended way.

> **Attention**: This procedure needs an external Traefik network. If this has not yet been done, it must be created beforehand:
>
> ```bash
> docker network create traefik-public
> ```

### 1.2.1 Start LAMP stack

```bash
docker compose pull && docker compose up -d
```

### 1.2.2 Composer install

Use the user `user` to keep the permissions:

```bash
docker compose exec -u user php composer install
```

### 1.2.3 Show version

#### Command Line

```bash
docker compose exec -u user php bin/console version:show
```

```bash
  Version:         0.33.0
  License:         Copyright (c) 2022 Björn Hempel
  Authors:         Björn Hempel <bjoern@hempel.li>
  PHP Version:     8.1.12
  Symfony Version: 6.1.7
```

### 1.2.4 Reinitialize the test and development database

Required for functional tests (test database):

```bash
docker compose exec -u user php bin/console db:reinitialize --env=test
```

Check db size:

```bash
docker compose exec -u user php du -h var/cache/test/test.db
```

```text
232K    var/cache/test/test.db
```

### 1.2.5 Run first test

```bash
docker compose exec -u user php composer test
```

## 1.3 Install dependencies

```bash
docker compose exec php /etc/init.d/supervisor start
```

```bash
docker compose exec php composer install
```

```bash
docker compose exec php yarn install
```

```bash
docker compose exec php yarn encore production
```

```bash
docker compose exec php composer migrate-prod
```

# 2. Requirements

* PHP 8.1

## 2.1 PHP Modules

```bash
php -m
```

```bash
[PHP Modules]
bcmath
calendar
Core
ctype
curl
date
dom
exif
FFI
fileinfo
filter
ftp
gettext
hash
iconv
imagick
intl
json
libxml
mbstring
mysqli
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
Reflection
session
shmop
SimpleXML
sockets
sodium
SPL
sqlite3
standard
sysvmsg
sysvsem
sysvshm
tokenizer
xdebug
xml
xmlreader
xmlwriter
xsl
Zend OPcache
zlib

[Zend Modules]
Xdebug
Zend OPcache
```

# 3. API Documentation

* [API - v1](docs/api/README.md)

# 4. Run tests

Test DB preparations if needed:

```bash
bin/console db:reinitialize --env=test
```

Check sqlite db:

```bash
ls -la var/app.db
```

## 4.1 General tests

Runs all available tests (phpmnd, phpunit, phpstan, phpcs, rector):

```bash
composer test
```

or

```bash
composer test:all
```

Runs basic tests (phpunit, phpstan):

```bash
composer test:basic
```

Runs most tests (phpmnd, phpunit, phpstan, phpcs):

```bash
composer test:most
```

## 4.2 PHP Coding Standards Fixer

* **Web**: https://cs.symfony.com/
* **Github**: https://github.com/FriendsOfPHP/PHP-CS-Fixer
* **Wikipedia**: https://en.wikipedia.org/wiki/PHP_Standard_Recommendation

We use the PSR-12 standard rule in this project.

### 4.2.1 Dry-Run

Runs PHP Coding Standards Fixer for all folders (dry-run) and check for inconsistencies. No sources are changed in
the process:

```bash
composer phpcs:check:all
```

Runs PHP Coding Standards Fixer for the src folder (dry-run) and check for inconsistencies. No sources are changed in
the process:

```bash
composer phpcs:check:src
```

Runs PHP Coding Standards Fixer for the tests folder (dry-run) and check for inconsistencies. No sources are changed in
the process:

```bash
composer phpcs:check:tests
```

### 4.2.2 Fix source code

Runs PHP Coding Standards Fixer for all folders (fix). The source code is tried to be adapted according to the
defined rules:

```bash
composer phpcs:fix:all
```

Runs PHP Coding Standards Fixer for the src folder (fix). The source code is tried to be adapted according to the
defined rules:

```bash
composer phpcs:fix:src
```

Runs PHP Coding Standards Fixer for the tests folder (fix). The source code is tried to be adapted according to the
defined rules:

```bash
composer phpcs:fix:tests
```

## 4.3 PHP Mess Detector

* **Web**: https://phpmd.org/
* **Github**: https://github.com/phpmd/phpmd
* **Other**: https://phpqa.io/projects/phpmd.html

To get more information on this topic, run the following command:

```bash
composer | grep "phpmd:"
```

## 4.4 PHP Magic Number Detector

* **Github**: https://github.com/povils/phpmnd
* **Wikipedia**: https://en.wikipedia.org/wiki/Magic_number_(programming)

Runs PHP Magic Number Detector and detects magic numbers for all folders:

```bash
composer phpmnd:all
```

Runs PHP Magic Number Detector and detects magic numbers for the src folder:

```bash
composer phpmnd:src
```

Runs PHP Magic Number Detector and detects magic numbers for the tests folder:

```bash
composer phpmnd:tests
```

## 4.5 PHPStan

* **Web**: https://phpstan.org/
* **Github**: https://github.com/phpstan/phpstan
* **Wikipedia**: https://en.wikipedia.org/wiki/Static_program_analysis

```bash
composer phpstan:run
```

or

```bash
composer phpstan
```

## 4.6 PHPUnit

* **Web**: https://phpunit.de/
* **Github**: https://github.com/sebastianbergmann/phpunit

Runs all available PHPUnit tests (api, functional, unit):

```bash
composer phpunit
```

or

```bash
composer phpunit:all
```

Runs only unit PHPUnit tests:

```bash
composer phpunit:api
```

Runs only functional PHPUnit tests:

```bash
composer phpunit:functional
```

Runs only unit PHPUnit tests:

```bash
composer phpunit:unit
```

## 4.7 PHP Rector

* **Web**: https://getrector.org/
* **Github**: https://github.com/rectorphp/rector

Runs PHP Rector and does a dry-run:

```bash
composer rector:check
```

Runs PHP Rector and fix source code:

```bash
composer rector:fix
```

# 5. Docker Compose

## 5.1 Basic Commands

### 5.1.1 Start LAMP stack

```bash
docker compose up -d
```

### 5.1.2 Stop LAMP stack

```bash
docker compose down
```

### 5.1.3 Rebuild images

```bash
docker compose build
```

### 5.1.4 Load dumps into database

All SQL dumps from the `fixtures/db` folder will be imported:

```bash
for dump in fixtures/db/*.sql; do mysql -h127.0.0.1 -P3306 -uphp-calendar-api -pphp-calendar-api --default-character-set=utf8 opa < "$dump"; done
```

or

```bash
composer db:initialize:main
```

### 5.1.5 Create dumps from database

The current database is exported to the `fixtures/db` folder:

```bash
bin/dbHelper dump
```

## 5.2 Show versions

### 5.2.1 Composer

```bash
docker compose exec -u user php php -v
```

```text
PHP 8.1.12 (cli) (built: Oct 28 2022 18:32:13) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.1.12, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.12, Copyright (c), by Zend Technologies
```

### 5.2.2 Composer

```bash
docker compose exec -u user php composer -V
```

```text
Composer version 2.4.4 2022-10-27 14:39:29
```

### 5.2.3 Symfony CLI

```bash
docker compose exec -u user php symfony -V
```

```text
Symfony CLI version 5.4.13 (c) 2017-2022 Symfony SAS #StandWithUkraine Support Ukraine (2022-08-16T08:17:04Z - stable)
```

### 5.2.4 Symfony Framework

```bash
docker compose exec -u user php bin/console --version
```

```text
Symfony 6.1.7 (env: dev, debug: true) #StandWithUkraine https://sf.to/ukraine
```

### 5.2.5 Application Version

```bash
docker compose exec -u user php bin/console version:show
```

```text
  Version:         0.33.0
  License:         Copyright (c) 2022 Björn Hempel
  Authors:         Björn Hempel <bjoern@hempel.li>
  PHP Version:     8.1.12
  Symfony Version: 6.1.7
```

```bash
docker compose exec -u user php bin/console version:show --format json
```

```json
{
  "version": "0.33.0",
  "license": "Copyright (c) 2022 Björn Hempel",
  "authors": [
    "Björn Hempel <bjoern@hempel.li>"
  ],
  "php-version": "8.1.12",
  "symfony-version": "6.1.7"
}
```

### 5.2.6 Node.js and npm

```bash
docker compose exec -u user php node -v
```

```text
v16.18.1
```

```bash
docker compose exec -u user php npm -v
```

```text
9.1.1
```

### 5.2.7 Yarn

```bash
docker compose exec -u user php yarn -v
```

```text
1.22.19
```

## 5.3 Docker container commands

### 5.3.1 List directories

```bash
docker compose exec -u user php ls -la
```

### 5.3.2 Show current user

```bash
docker compose exec -u user php id
```

```text
uid=1000(user) gid=1000(user) groups=1000(user)
```

## 5.4 Docker container login

### 5.4.1 PHP Container

```bash
docker compose exec -u user php bash
```
