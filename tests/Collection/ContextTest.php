<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Collection
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Collection;

use One\Collection\Context;

class ContextTest extends \PHPUnit\Framework\TestCase
{
    protected $context;

    public function setUp()
    {
        $this->context = new Context([
            'foo' => 'foo',
            'bar' => 'bar',
            'tar' => 'tar',
            'zar' => new \stdClass,
        ]);
    }

    public function tearDown()
    {
        $this->context = null;
    }

    public function testHasEmpty()
    {
        $this->assertFalse((new Context)->has('foo'));
    }

    /**
     * @dataProvider hasProvider
     */
    public function testHas($key, $result)
    {
        $this->assertEquals($this->context->has($key), $result);
    }

    public function hasProvider()
    {
        return [
            ['', false],
            ['foo', true],
        ];
    }

    public function testAll()
    {
        $this->assertCount(4, $this->context->all());
    }

    public function testUnset()
    {
        $this->context->unset('FOO');
        $this->assertCount(3, $this->context->all());
    }

    /**
     * @dataProvider getProvider
     */
    public function testGet($key, $default, $class, $value)
    {
        $this->assertEquals($this->context->get($key, $default, $class), $value);
    }

    public function getProvider()
    {
        return [
            ['FOO', null, null, 'foo'],
            ['bad', null, null, null],
            ['foo', null, '\\PHPUnit\\Framework\\TestCase', null],
            ['zar', null, '\\stdClass', new \stdClass],
        ];
    }

    public function testSet()
    {
        $this->context->set('NEW', 'new');
        $this->assertEquals($this->context->get('new'), 'new');
    }

    public function testSetMulti()
    {
        $this->context->setMulti([
            'a' => 'a',
            'b' => 'b'
        ]);

        $this->assertTrue($this->context->has('a') && $this->context->has('b'));
    }
}
