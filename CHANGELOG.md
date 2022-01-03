# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Releases

### [0.1.2] - 2022-01-03

* #13

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
