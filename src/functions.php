<?php

namespace Iadvize\SwaggerizeFastRoute;

use FastRoute\RouteCollector;
use Iadvize\SwaggerizeFastRoute\OperationParser\OperationParserInterface;
use Swagger\Document;
use Swagger\OperationReference;

/**
 * Scan the json file and build routes
 *
 * @param string                   $file            The file path
 * @param RouteCollector           $routeCollector  Route collector
 * @param OperationParserInterface $operationParser Operation parser
 */
function scan($file, RouteCollector $routeCollector, OperationParserInterface $operationParser)
{
    $json = file_get_contents($file);

    $swagger = json_decode($json);

    $document = new Document($swagger);

    /** @var OperationReference[] $operations */
    $operations = $document->getOperationsById();

    foreach ($operations as $operation) {
        $routeCollector->addRoute($operation->getMethod(), $operation->getPath(), $operationParser->getHandler($operation));
    }
}
