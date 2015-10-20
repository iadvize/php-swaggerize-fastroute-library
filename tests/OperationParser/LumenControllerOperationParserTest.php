<?php

namespace IadvizeTest\OperationParser;

use Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser;
use Iadvize\SwaggerizeFastRoute\OperationParser\OperationParserInterface;
use Swagger\OperationReference;

/**
 * Test LumenControllerOperationParser
 *
 */
class LumenControllerOperationParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var OperationParserInterface */
    protected $operationParser;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->operationParser = new LumenControllerOperationParser('Iadvize\Test\\');
    }

    /**
     * Test: Get handler should return an Lumen compatible Array with controller@method
     */
    public function testGetHandler()
    {
        $operationMock = \Mockery::mock(OperationReference::class);
        $operationMock->shouldReceive('getPath')->andReturn('/marco');
        $operationMock->shouldReceive('getMethod')->andReturn('GET');
        $operationMock->shouldReceive('getOperationId')->andReturn('operationID');

        $this->assertEquals(['uses' => 'Iadvize\Test\MarcoController@get', 'as' => 'operationID'], $this->operationParser->getHandler($operationMock));
    }

    /**
     * Test: Get handler should return an Lumen compatible Array with controller@method
     */
    public function testGetHandlerWithRouteParameter()
    {
        $operationMock = \Mockery::mock(OperationReference::class);
        $operationMock->shouldReceive('getPath')->andReturn('/marco/{id}');
        $operationMock->shouldReceive('getMethod')->andReturn('PUT');
        $operationMock->shouldReceive('getOperationId')->andReturn('operationID');

        $this->assertEquals(['uses' => 'Iadvize\Test\MarcoController@update', 'as' => 'operationID'], $this->operationParser->getHandler($operationMock));
    }

    /**
     * Test: Get handler should return an Lumen compatible Array with controller@method
     */
    public function testGetHandlerWithDeepPath()
    {
        $operationMock = \Mockery::mock(OperationReference::class);
        $operationMock->shouldReceive('getPath')->andReturn('/marco/{id}/name/first');
        $operationMock->shouldReceive('getMethod')->andReturn('POST');
        $operationMock->shouldReceive('getOperationId')->andReturn('operationID');

        $this->assertEquals(['uses' => 'Iadvize\Test\Marco\Name\FirstController@create', 'as' => 'operationID'], $this->operationParser->getHandler($operationMock));
    }
}
