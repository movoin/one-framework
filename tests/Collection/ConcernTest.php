<?php
declare(strict_types=1);
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

use One\Collection\Concern\HasCollection;
use One\Collection\Concern\HasContainer;
use One\Collection\Concern\HasContext;
use One\Collection\Concern\HasContextGetter;
use One\Collection\Collection;
use One\Collection\Container;
use One\Collection\Context;

class ConcernTest extends \PHPUnit\Framework\TestCase
{
    use HasCollection;
    use HasContainer;
    use HasContext;
    use HasContextGetter;

    /**
     * @dataProvider allProvider
     */
    public function testAll($name, $object)
    {
        $this->assertInstanceOf(get_class($object), call_user_func_array([$this, 'get' . $name], []));
        call_user_func_array([$this, 'set' . $name], [$object]);
        $this->assertInstanceOf(get_class($object), call_user_func_array([$this, 'get' . $name], []));
    }

    public function allProvider()
    {
        return [
            ['Collection', new Collection],
            ['Container', new Container],
            ['Context', new Context],
        ];
    }

    public function testGetter()
    {
        $test = new B;

        $test->getContext()->set('foo', 'bar');

        $this->assertEquals($test->foo, 'bar');
        $this->assertEquals($test->bar, 'bar');
    }

    public function testSetter()
    {
        $test = new B;
        $test->foo = 'foo';

        $this->assertEquals($test->foo, 'foo');
    }

    public function testException()
    {
        $this->expectException('One\Collection\Exception\ContextValueNotFoundException');
        $this->bad;
    }
}

class A
{
    public $zar = 'bar';

    public function __get(string $name)
    {
        return $this->zar;
    }
}

class B extends A
{
    use HasContext;
    use HasContextGetter;
}
