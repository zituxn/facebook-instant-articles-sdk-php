# Facebook Instant Articles PHP SDK #

The Facebook Instant Articles SDK for PHP provides a native interface for creating and publishing Instant Articles. The SDK enables developers to more easily integrate Instant Articles into content management systems and in turn enables journalist and publishers to easily publish Instant Articles.

The SDK consists of three components:
- **Elements**: A domain-specific language for creating an Instant Articles structure that strictly follows the specification and can be automatically serialized into the subset of HTML5 markup used in the [Instant Articles format](https://developers.facebook.com/docs/instant-articles/reference). This language allows users to programmatically create Instant Articles that are guaranteed to be in compliance with the format.
- **Transformer**: An engine for transforming any markup into an Instant Article structure in the DSL. The engine runs a set of rules on the markup that will specify the selection and transformation of elements output by the CMS into their Instant Articles counterparts. The transformer ships with a base set of rules for common elements (such as a basic paragraph or an image) that can be extended and customized by developers utilizing the SDK.
- **Client**: A simple wrapper around the [Instant Articles API](https://developers.facebook.com/docs/instant-articles/api), which can be used for publishing Instant Articles on Facebook. The client provides a CRUD interface for Instant Articles as well as a helper for authentication. The client depends on the main [Facebook SDK for PHP](https://github.com/facebook/facebook-php-sdk-v4) as an interface to the Graph API and Facebook Login.

## Installation

The Facebook Instant Articles PHP SDK can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
composer require facebook/facebook-instant-articles-sdk-php
```

## Build it and develop ##

[Composer](https://getcomposer.org/) is a prerequisite for building and developing. [Install composer globally](https://getcomposer.org/doc/00-intro.md#globally), then run `composer install` to install required files.

* Open a Terminal. Run these steps:
```sh
cd facebook-instant-articles-sdk-php
php composer.phar self-update
php composer.phar install
```
* To run the tests:
```sh
./vendor/bin/phpunit
```
* Everytime you change structure, paths, namespaces etc, make sure you run again the autoload generator
```sh
php composer.phar dump-autoload
```

Note: `php` refers to the location of your php executable, if its not on your system's path

## Contributing

For us to accept contributions you will have to first have signed the [Contributor License Agreement](https://code.facebook.com/cla). Please see [CONTRIBUTING](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/CONTRIBUTING.md) for details.

## License

Please see the [license file](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/LICENSE) for more information.
