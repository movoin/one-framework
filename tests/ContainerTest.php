<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests;

use One\Container;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container;
    }

    public function tearDown()
    {
        $this->container = null;
    }

    public function testSetAlias()
    {
        $this->container->alias('bar', 'foo');
        $this->assertTrue($this->container->has('foo'));
    }

    public function testBinding()
    {
        $this->container->bind('foo', function ($container, $parameters) {
            return new \stdClass;
        });

        $this->assertTrue($this->container->has('foo'));
        $this->assertEquals(new \stdClass, $this->container->get('foo'));

        $this->container->bind('\\stdClass');
        $this->assertEquals(new \stdClass, $this->container->get('\\stdClass'));

        $this->container->bind('stdClass', '\\stdClass');
        $this->assertEquals(new \stdClass, $this->container->get('stdClass'));
    }

    public function testMake()
    {
        $this->container->alias('\\stdClass', 'foo');
        $std = $this->container->make('\\stdClass');

        $this->assertSame($std, $this->container->get('foo'));
    }

    /**
     * @expectedException One\Exception\ContainerException
     */
    public function testGetObjectException()
    {
        $this->container->get('One/NotFounded/Class');
    }

    /**
     * @expectedException One\Exception\ContainerException
     */
    public function testResolveException()
    {
        $this->container->resolve('One/ReflectionException/Class');
    }
}
