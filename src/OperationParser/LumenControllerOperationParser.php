<?php

namespace Iadvize\SwaggerizeFastRoute\OperationParser;

use Swagger\OperationReference;

/**
 * Class LumenControllerOperationParser
 *
 * @package Iadvize\SwaggerizeFastRoute\OperationParser
 */
class LumenControllerOperationParser implements OperationParserInterface
{

    /** @var string */
    protected $namespace;

    /** @var array */
    private $httpVerbToControllerMethod = [
        'GET'     => 'get',
        'POST'    => 'create',
        'PUT'     => 'update',
        'HEAD'    => 'head',
        'OPTIONS' => 'options',
        'PATCH'   => 'patch',
        'DELETE'  => 'delete',
    ];

    /**
     * Constructor
     *
     * @param string $controllerNamespace
     */
    public function __construct($controllerNamespace)
    {
        if (strpos($controllerNamespace, '\\') === strlen($controllerNamespace) - 1) {
            $controllerNamespace = mb_substr($controllerNamespace, 0, -1);
        }

        $this->namespace = $controllerNamespace;
    }

    /**
     * Get Handler
     *
     * @param OperationReference $operation
     *
     * @return array
     */
    public function getHandler(OperationReference $operation)
    {
        // remove route parameters
        $path = preg_replace('/\/\{.*\}/', '', $operation->getPath());

        // lowerCamelCase to UpperCamelCase
        $paths = explode('/', $path);
        // path start with a /
        unset($paths[0]);
        foreach ($paths as &$path) {
            $path[0] = strtoupper($path[0]);
        }
        // path to 'relative' namespace
        $path = implode('\\', $paths);

        $controller = $this->namespace . $path . 'Controller';

        return ['uses' => $controller . '@' . $this->httpVerbToControllerMethod[strtoupper($operation->getMethod())], 'as' => $operation->getOperationId()];
    }
}
