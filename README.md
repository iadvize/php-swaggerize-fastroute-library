# php-swaggerize-fastroute-library
A library to automatically create FastRoute routes based on swagger JSON documentation
# Install
To install with composer:
```
composer require iadvize/php-swaggerize-fastroute-library
```

# Generate route File (FastRoute compatible)

```
vendor/bin/swaggerize swagger:scan path/to/swagger/json controllers\namespace [--routeFile=route/file/path]
```

# Dispatch generated file or simply use cache

You can then use FastRoute cached dispatcher to use generated file or directly use a cache dispatcher (file will be generated at first call).

```PHP
<?php

require '/path/to/vendor/autoload.php';

$lumenOperationParser = new \Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser('Controllers\\Namespace\\');

$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r, ['cacheFile' => 'route/file/path']) {
    \Iadvize\SwaggerizeFastRoute\addRoutes(
        'https://raw.githubusercontent.com/wordnik/swagger-spec/master/examples/v2.0/json/petstore.json',
         $r,
         $lumenOperationParser,
         ['routeFile' => 'path/to/generated/route/file']
     );
});

// Alternatively use cache
/**
$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r, ['cacheFile' => 'route/file/path']) {
    \Iadvize\SwaggerizeFastRoute\addRoutes(
        'https://raw.githubusercontent.com/wordnik/swagger-spec/master/examples/v2.0/json/petstore.json',
         $r,
         $lumenOperationParser,
         ['routeFile' => 'path/to/generated/route/file', 'cacheEnabled' => true]
     );
});
*/

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        break;
}
```

# Apply this to Lumen application

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

# How handler is formed

Handlers are formed from route defined in swagger as [Lumen](http://lumen.laravel.com/docs/routing#named-routes) define it for controller class : `Controller@method`
### Controller class generation

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

### Method generation

Controller method is mapped from HTTP method :
* `GET`     => `get`,
* `POST`    => `create`,
* `PUT`     => `update`,
* `HEAD`    => `head`,
* `OPTIONS` => `options`,
* `PATCH`   => `patch`,
* `DELETE`  => `delete`,

