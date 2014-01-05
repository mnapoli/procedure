<?php

namespace Procedure\Test\FunctionLoader;

use Procedure\FunctionLoader\PSR4FunctionLoader;

/**
 * @covers \Procedure\FunctionLoader\PSR4FunctionLoader
 */
class PSR4FunctionLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testLoad($function, $namespace = null, $directory = null)
    {
        $loader = new PSR4FunctionLoader();

        $loader->registerNamespaces($namespace, __DIR__ . '/Fixtures/' . $directory);

        $this->assertEquals($function, $loader->load($function));
    }

    public function provider()
    {
        return [
            ['foo\test', 'foo', 'foo'],
            ['bar\baz\test', 'bar', 'bar'],
        ];
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to load function bar\bam
     */
    public function testLoadNoMatch()
    {
        $loader = new PSR4FunctionLoader();

        $loader->registerNamespaces('foo', __DIR__ . '/Fixtures/foo');

        $loader->load('bar\bam');
    }
}
