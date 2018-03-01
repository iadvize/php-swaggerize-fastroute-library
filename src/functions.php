<?php

namespace Iadvize\SwaggerizeFastRoute;

use FastRoute\RouteCollector;
use Iadvize\SwaggerizeFastRoute\OperationParser\OperationParserInterface;
use Swagger\Document;
use Swagger\OperationReference;

// @codingStandardsIgnoreFile

/**
 * Scan the json file and build routes
 *
 * @param string                   $file            The file path
 * @param OperationParserInterface $operationParser Operation parser
 *
 * @return array
 */
function scan($file, OperationParserInterface $operationParser)
{
    $json = file_get_contents($file);

    if (!$json) {
        throw new \LogicException($file . ' can not be read');
    }

    $swagger = json_decode($json);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \LogicException($file . ' is not a valid json file');
    }

    $document = new Document($swagger);

    /** @var OperationReference[] $operations */
    $operations = $document->getOperationsById();

    $routes = [];

    foreach ($operations as $operation) {
        $routes[] = [
            'method' => $operation->getMethod(),
            'uri'    => $operation->getPath(),
            'action' => $operationParser->getHandler($operation),
        ];
    }

    return $routes;
}

/**
 * Add route to route collector
 *
 * @param string                   $swaggerJson     Swagger json file path (can be an URL)
 * @param RouteCollector           $routeCollector  FastRoute route collector
 * @param OperationParserInterface $operationParser Swagger operation parser.
 * @param array                    $options         Options (@see config/swaggerConfig.dist.php)
 */
function addRoutes($swaggerJson, RouteCollector $routeCollector, OperationParserInterface $operationParser, $options = [])
{
    if (!isset($options['routeFile']) || !file_exists($options['routeFile'])) {
        $routes = scan($swaggerJson, $operationParser);
    } else {
        $routes = require $options['routeFile'];
    }

    if (isset($options['routeFile']) && isset($options['cacheEnabled']) && $options['cacheEnabled']) {
        cacheRoutes($routes, $options['routeFile']);
    }

    foreach ($routes as $route) {
        $routeCollector->addRoute($route['method'], $route['uri'], $route['action']);
    }
}

/**
 * Write routes array into a file
 *
 * @param array  $routes Routes
 * @param string $stream File name or stream in which write routes.
 */
function cacheRoutes(array $routes, $stream)
{
    if (is_writable($stream)) {
        throw new \LogicException($stream . ' is not writable');
    }

    $routeResource = fopen($stream, 'w');

    $serializedRoutes = var_export($routes, true);

    fwrite($routeResource, '<?php return ' . $serializedRoutes . ';');
}
