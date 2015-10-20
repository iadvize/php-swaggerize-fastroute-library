# php-swaggerize-fastroute-library
A library to automatically create FastRoute routes based on swagger JSON documentation
# Install
To install with composer:
```
composer require iadvize/php-swaggerize-fastroute-library
```
# Dispatch swagger app

```
<?php

require '/path/to/vendor/autoload.php';

$lumenOperationParser = new \Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser('Controllers\\Namespace\\');

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    \Iadvize\SwaggerizeFastRoute\scan('https://raw.githubusercontent.com/wordnik/swagger-spec/master/examples/v2.0/json/petstore.json', $r, $lumenOperationParser);
});

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
# Use a cache dispatcher

Parse swagger JSON and create route dynamically at each API call can be heavy. If you need performance, use FastRoute cachedDispatcher

```
<?php

require '/path/to/vendor/autoload.php';

$lumenOperationParser = new \Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser('Controllers\\Namespace\\');

$dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r, ['cacheFile' => 'path/to/cache/file']) {
    \Iadvize\SwaggerizeFastRoute\scan('https://raw.githubusercontent.com/wordnik/swagger-spec/master/examples/v2.0/json/petstore.json', $r, $lumenOperationParser);
});

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

