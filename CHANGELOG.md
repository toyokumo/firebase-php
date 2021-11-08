# CHANGELOG

## [Unreleased]
### Changed
* [Active support for PHP 7.4 ends Q4 2021](https://www.php.net/supported-versions.php). This SDK will therefore not be supported on PHP 7.x anymore.
* It is now mandatory to provide a Firebase Project ID. When the project ID cannot be determined from the
  provided credentials (usually a service account), it can be provided by setting the `GOOGLE_CLOUD_PROJECT=<project-id>`
  environment variable.

### Removed
* Removed local phone number validation when `giggsey/libphonenumber-for-php` was installed. Phone numbers are
  validated by the Firebase Service in any case, and even when a phone number was considered valid, in rare
  cases the Firebase API rejected them still.
* Removed support for the `FIREBASE_CREDENTIALS` environment variable to be used for credential discovery. 
  `GOOGLE_APPLICATION_CREDENTIALS` was already supported and is the same environment variable the official
  Google Libraries use as well.
* Dropped support for Guzzle 6.x
* Removed deprecated methods
  * `Auth::setCustomUserAttributes()`, use `Auth::setCustomUserClaims()` instead
  * `Auth::deleteCustomUserAttributes()`, use `Auth::setCustomUserClaims()` with null values instead
  * `Auth\UserRecord::$customAttributes`, use `Auth\UserRecord::$customClaims` instead
  * `Factory::withEnabledDebug()`, use `Factory::withHttpDebugLogger()` instead
* Removed deprecated/obsolete internal classes and methods)

[Unreleased]: https://github.com/kreait/firebase-php/compare/5.x...6.x
