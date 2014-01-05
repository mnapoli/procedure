<?php

namespace Procedure\Test;

use DI\ContainerInterface;
use Procedure\Controller\FunctionControllerResolver;
use Procedure\FunctionLoader\FunctionLoaderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Procedure\Controller\FunctionControllerResolver
 */
class FunctionControllerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testNonRoutableRequest()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);

        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $this->assertFalse($controllerResolver->getController(new Request()));
    }

    public function testRoutableRequest()
    {
        $controller = function () {
            return 'bar';
        };

        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $functionLoader->expects($this->once())
            ->method('load')
            ->with('foo')
            ->will($this->returnValue($controller));

        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $request = new Request([], [], ['_controller' => 'foo']);

        $this->assertSame($controller, $controllerResolver->getController($request));
    }

    public function testGetArgumentsEmpty()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $arguments = $controllerResolver->getArguments(new Request(), 'Procedure\Test\testEmpty');

        $this->assertInternalType('array', $arguments);
        $this->assertEmpty($arguments);
    }

    public function testGetArgumentsRequest()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $request = new Request();

        $arguments = $controllerResolver->getArguments($request, 'Procedure\Test\testRequest');

        $this->assertInternalType('array', $arguments);
        $this->assertEquals([$request], $arguments);
    }

    public function testGetArgumentsRequestParameter()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $request = new Request(['foo' => 'bar']);

        $arguments = $controllerResolver->getArguments($request, 'Procedure\Test\testRequestParameter');

        $this->assertInternalType('array', $arguments);
        $this->assertEquals(['bar'], $arguments);
    }

    public function testGetArgumentsContainerEntry()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with(\stdClass::class)
            ->will($this->returnValue(true));
        $container->expects($this->once())
            ->method('get')
            ->with(\stdClass::class)
            ->will($this->returnValue('foo'));

        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $arguments = $controllerResolver->getArguments(new Request(), 'Procedure\Test\testContainerEntry');

        $this->assertInternalType('array', $arguments);
        $this->assertEquals(['foo'], $arguments);
    }

    public function testGetArgumentsDefaultValue()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $arguments = $controllerResolver->getArguments(new Request(), 'Procedure\Test\testDefaultValue');

        $this->assertInternalType('array', $arguments);
        $this->assertEquals(['foo'], $arguments);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unguessable parameter "param" for function Procedure\Test\testUnguessableArgument()
     */
    public function testGetArgumentsUnguessableArgument()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $functionLoader = $this->getMockForAbstractClass(FunctionLoaderInterface::class);
        $controllerResolver = new FunctionControllerResolver($container, $functionLoader);

        $controllerResolver->getArguments(new Request(), 'Procedure\Test\testUnguessableArgument');
    }
}

function testEmpty()
{
}

function testRequest(Request $request)
{
}

function testRequestParameter($foo)
{
}

function testContainerEntry(\stdClass $param)
{
}

function testDefaultValue($param = 'foo')
{
}

function testUnguessableArgument($param)
{
}
