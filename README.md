 php-swaggerize-fastroute-library [![CircleCI](https://circleci.com/gh/iadvize/php-swaggerize-fastroute-library.svg?style=svg)](https://circleci.com/gh/iadvize/php-swaggerize-fastroute-library) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iadvize/php-swaggerize-fastroute-library/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iadvize/php-swaggerize-fastroute-library/?branch=master)
=================================

> **⚠️ WARNING**: This repository is deprecated and no longer maintained since 2015. See removal guide [here](#removal-guide).

A library to automatically create FastRoute routes based on swagger JSON documentation

## Removal guide

This library is deprecated and no longer maintained since 2015. Here is a guide to remove it from your project:

- check that your app uses this library `iadvize/php-swaggerize-fastroute-library` (search for `Iadvize\SwaggerizeFastRoute` package import in php code). If it does not, you can safely remove it from your `composer.json` and skip the next steps.
- if your CI/CD or dockerfile uses the `swaggerize swagger:scan` command, you should:
  - run it manually once (use your full existing command, eg: `./vendor/bin/swaggerize swagger:scan storage/docs/definition.json "path/to/controllers" --routeFile somewhere/routeFile.php`)
  - remove the `swaggerize swagger:scan` command from your CI/CD or dockerfile
- convert the generated route file to a standard FastRoute setup (see [FastRoute documentation](https://github.com/nikic/FastRoute?tab=readme-ov-file#usage)), usually the same file as the one using the `Iadvize\SwaggerizeFastRoute` package.
- remove the `Iadvize\SwaggerizeFastRoute` package import from your php code and all usage.
- remove the `iadvize/php-swaggerize-fastroute-library` from your `composer.json` (eg: `composer remove iadvize/php-swaggerize-fastroute-library`)
- delete the routeFile.php file (not needed anymore)

## Examples

### Generate route File (FastRoute compatible)

```
vendor/bin/swaggerize swagger:scan path/to/swagger/json controllers\namespace [--routeFile=route/file/path]
```

## Install

To install with composer:
```
composer require iadvize/php-swaggerize-fastroute-library
```

## Documentation

### Generate route File (FastRoute compatible)

```
vendor/bin/swaggerize swagger:scan path/to/swagger/json controllers\namespace [--routeFile=route/file/path]
```

### Dispatch generated file or simply use cache

You can then use FastRoute cached dispatcher to use generated file or directly use a cache dispatcher (file will be generated at first call).

```PHP
<?php

require '/path/to/vendor/autoload.php';

$lumenOperationParser = new \Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser('Controllers\\Namespace\\');

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r, ['cacheFile' => 'route/file/path']) {
    \Iadvize\SwaggerizeFastRoute\addRoutes(
        'path/to/swagger/json',
         $r,
         $lumenOperationParser,
         ['routeFile' => 'path/to/generated/route/file', 'cacheEnabled' => false]
     );
});

// Fetch method and URI from somewhere
// ... see FastRoute Dispatcher
```

Alternatively to generate routes, you can simply cache first parse by setting `'cacheEnabled' => true` in addRoute function.

### Apply this to Lumen application

To use this swagger routes in a Lumen Application (which use FastRoute as route library), you need to extends `Laravel\Lumen\Application` and override `createDispatcher` method.

```PHP

<?php

namespace My\Application;

use Laravel\Lumen\Application as LumenApplication;

/**
 * Class Application
 *
 * @package My\Application
 */
class Application extends LumenApplication
{
    /**
     * {@inheritdoc}
     */
    protected function createDispatcher()
    {
        return $this->dispatcher ?: \FastRoute\simpleDispatcher(function ($r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route['method'], $route['uri'], $route['action']);
            }

            $operationParser = new \Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser('My\Application\Http\Controllers');

            \Iadvize\SwaggerizeFastRoute\addRoutes(storage_path('docs/definition.json'), $r, $operationParser, ['routeFile' => 'route/file/path']);
        });
    }
}
```

### How handler is formed

Handlers are formed from route defined in swagger as [Lumen](http://lumen.laravel.com/docs/routing#named-routes) define it for controller class : `Controller@method`

#### Controller class generation

Controller class is determined from path route with first character uppercased and with Controller at the end of file name

This swagger JSON :

```JSON
{
// ...
  "paths": {
    "/pets": {
      "get": {
        // ...
      }
      "put": {
        // ...
      }
    }
    "/store": {
      "post": {
        // ...
      }
    }
  }
// ...
}
```

will generates respectively this handlers:

* `PetsController@get`
* `PetsController@update`
* `StoreController@create`

#### Method generation

Controller method is mapped from HTTP method :
* `GET`     => `get`,
* `POST`    => `create`,
* `PUT`     => `update`,
* `HEAD`    => `head`,
* `OPTIONS` => `options`,
* `PATCH`   => `patch`,
* `DELETE`  => `delete`,

## Contribute

Look at contribution guidelines here : [CONTRIBUTING.md](CONTRIBUTING.md)
