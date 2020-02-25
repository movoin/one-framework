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

use One\Collection\Collection;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testSet()
    {
        $coll = new Collection;
        $coll->set('foo', 'bar');

        $this->assertTrue($coll->has('foo'));

        $coll->set(null, 'bar');

        $this->assertTrue($coll->count() === 2);
    }

    public function testGet()
    {
        $coll = new Collection(['foo' => 'bar']);

        $this->assertEquals($coll->get('foo'), 'bar');
        $this->assertEquals($coll->all(), ['foo' => 'bar']);
        $this->assertEquals($coll->toArray(), ['foo' => 'bar']);
        $this->assertEquals($coll->toJson(), '{"foo":"bar"}');
    }

    public function testShiftPop()
    {
        $coll = new Collection(['foo', 'bar', 'zar']);

        $this->assertEquals($coll->shift(), 'foo');
        $this->assertEquals($coll->pop(), 'zar');
    }

    public function testRemove()
    {
        $coll = new Collection(['foo' => 'bar']);
        $this->assertTrue($coll->isNotEmpty());

        $coll->remove('foo');
        $this->assertTrue($coll->isEmpty());
    }

    public function testTake()
    {
        $coll = new Collection(['foo', 'bar', 'zar']);

        $this->assertCount(2, $coll->take(2));
    }

    public function testOnly()
    {
        $coll = new Collection([
            'foo' => 'foo',
            'bar' => 'bar',
            'zar' => 'zar',
            'tar' => 'tar',
        ]);

        $this->assertEquals($coll->only('foo', 'bar')->all(), ['foo' => 'foo', 'bar' => 'bar']);
    }

    public function testWhen()
    {
        $result = 0;
        $coll = new Collection(['foo']);

        $coll->whenNotEmpty(function ($c, $value) use (&$result) {
            $result = 1;
        });
        $this->assertTrue($result === 1);

        $coll->remove(0);
        $coll->whenEmpty(function ($c, $value) use (&$result) {
            $result = 2;
        });
        $this->assertTrue($result === 2);

        $coll->set(null, 'foo');
        $coll->whenEmpty(function ($c, $value) {}, function ($c, $value) use (&$result) {
            $result = 3;
        });
        $this->assertTrue($result === 3);

        $return = $coll->whenEmpty(function ($c, $value) {});
        $this->assertCount(1, $return);
    }

    public function testFilter()
    {
        $coll = new Collection(['foo', '', 'bar']);

        $this->assertCount(2, $coll->filter());

        $coll = new Collection([
            ['id' => 1, 'name' => 'foo', 'type' => 'hihao'],
            ['id' => 2, 'name' => 'bar', 'type' => 'hihao'],
            ['id' => 3, 'name' => 'zar', 'type' => 'hihao1']
        ]);
        $this->assertCount(2, $coll->filter(function ($item) {
            return $item['type'] === 'hihao';
        }));
    }

    public function testMap()
    {
        $coll = new Collection([1,2,3]);

        $this->assertEquals($coll->map(function ($value) {
            return $value * 2;
        })->all(), [2,4,6]);
    }

    public function testEach()
    {
        $coll = new Collection([1,2,3]);
        $result = 0;
        $coll->each(function ($value) use (&$result) {
            $result++;
        });

        $this->assertEquals($result, 3);

        $result = 0;
        $coll->each(function ($value) use (&$result) {
            $result++;
            if ($result === 2) {
                return false;
            }
        });

        $this->assertEquals($result, 2);
    }

    public function testFlip()
    {
        $coll = new Collection([
            'foo' => 'FOO',
            'bar' => 'BAR'
        ]);

        $this->assertEquals($coll->flip()->all(), [
            'FOO' => 'foo',
            'BAR' => 'bar',
        ]);
    }

    public function testKeys()
    {
        $coll = new Collection([
            'foo' => 'FOO',
            'bar' => 'BAR'
        ]);

        $this->assertEquals($coll->keys()->all(), [ 'foo', 'bar' ]);
    }

    public function testMerge()
    {
        $coll = new Collection([
            'foo' => '1',
            'bar' => '2'
        ]);

        $this->assertEquals($coll->merge(['zar' => '3'])->all(), [
            'foo' => '1',
            'bar' => '2',
            'zar' => '3',
        ]);
    }

    public function testIterator()
    {
        $coll = new Collection([
            'foo' => '1',
            'bar' => '2'
        ]);

        $result = 0;

        foreach ($coll as $key => $value) {
            if ($key === 'foo' || $key === 'bar') {
                $result++;
            }
        }

        $this->assertEquals($result, 2);
    }
}
