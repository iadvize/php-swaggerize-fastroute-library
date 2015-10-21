<?php

namespace Iadvize\SwaggerizeFastRoute\OperationParser;

use Swagger\OperationReference;

/**
 * Interface OperationParserInterface
 *
 * @package Iadvize\SwaggerizeFastRoute\OperationParser
 */
interface OperationParserInterface
{
    /**
     * Get Handler
     *
     * @param OperationReference $operation
     *
     * @return array
     */
    public function getHandler(OperationReference $operation);
}
