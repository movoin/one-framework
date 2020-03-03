<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Test\Utility\Helper
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Test\Utility\Helper;

use stdClass;
use One\Utility\Helper\ArrayHelper;
use One\Tests\Utility\Helper\Fixtures\Arrayable;
use One\Tests\Utility\Helper\Fixtures\ObjectClass;

class ArrayHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider setProvider
     */
    public function testSet($array, $key, $value, $result)
    {
        ArrayHelper::set($array, $key, $value);
        $this->assertEquals($array, $result);
    }

    public function setProvider()
    {
        return [
            [[], null, 'bar', ['bar']],
            [[], 'foo', 'bar', ['foo' => 'bar']],
            [[], 'foo.bar.zar', 'foobarzar', [
                'foo' => [
                    'bar' => [
                        'zar' => 'foobarzar'
                    ]
                ]
            ]],
            [['foo' => ['bar' => 'foobar']], 'foo', ['bar' => 'newbar'], ['foo' => ['bar' => 'newbar']]],
            [[], 'foo', ['bar' => 'newbar'], ['foo' => ['bar' => 'newbar']]],
            [[], 'foo.bar', 'newbar', ['foo' => ['bar' => 'newbar']]],
        ];
    }

    public function testInsert()
    {
        $array = ['foo', 'bar', 'tar', 'zar'];

        ArrayHelper::insert($array, 2, 'war');
        $this->assertEquals($array, ['foo', 'bar', 'war', 'tar', 'zar']);

        ArrayHelper::insert($array, 2, 'a', 'b', 'c');
        $this->assertEquals($array, ['foo', 'bar', 'a', 'b', 'c', 'war', 'tar', 'zar']);

        $array = [];

        ArrayHelper::insert($array, 2, 'war');
        $this->assertEquals($array, ['war']);
    }

    /**
     * @dataProvider arrayTypeProvider
     */
    public function testArrayType($method, $result, $array)
    {
        $this->assertEquals(
            forward_static_call_array(['One\Utility\Helper\ArrayHelper', $method], [$array]),
            $result
        );
    }

    public function arrayTypeProvider()
    {
        $indexed = [1,2,3,4];
        $associative = ['foo' => 1, 'bar' => 2];
        $mixed = [1, 2, 'foo' => 1, 'bar' => 2];

        return [
            ['isIndexed', true, $indexed],
            ['isIndexed', false, $associative],
            ['isIndexed', true, []],
            ['isIndexed', false, $mixed],
            ['isAssociative', true, $associative],
            ['isAssociative', false, $indexed],
            ['isAssociative', false, []],
            ['isAssociative', false, $mixed],
        ];
    }

    public function testGroup()
    {
        $array = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'foo'],
        ];

        $this->assertEquals(ArrayHelper::group($array, 'name'), [
            'foo' => [
                ['id' => 1, 'name' => 'foo'],
                ['id' => 2, 'name' => 'foo'],
            ]
        ]);

        $this->assertEquals(ArrayHelper::group([], 'name'), []);
    }

    public function testWhere()
    {
        $array = [
            ['id' => 1, 'name' => 'foo', 'type' => 'hihao'],
            ['id' => 2, 'name' => 'bar', 'type' => 'hihao'],
            ['id' => 3, 'name' => 'zar', 'type' => 'hihao'],
        ];

        $this->assertEquals(ArrayHelper::where([], function ($value, $key) { return true; }), []);

        $this->assertEquals(
            ArrayHelper::where($array, function ($elm) {
                return $elm['name'] !== 'zar';
            }),
            [
                ['id' => 1, 'name' => 'foo', 'type' => 'hihao'],
                ['id' => 2, 'name' => 'bar', 'type' => 'hihao'],
            ]
        );
    }

    public function testOnly()
    {
        $this->assertEquals(
            ArrayHelper::only(['id' => 4, 'name' => 'tar', 'type' => 'konichiwa'], ['name', 'type']),
            ['name' => 'tar', 'type' => 'konichiwa']
        );
    }

    /**
     * @dataProvider mapProvider
     */
    public function testMap($array, $from, $to, $group, $result)
    {
        $this->assertEquals(ArrayHelper::map($array, $from, $to, $group), $result);
    }

    public function mapProvider()
    {
        $array = [
            ['id' => 1, 'name' => 'foo', 'type' => 'nihao'],
            ['id' => 2, 'name' => 'bar', 'type' => 'nihao'],
            ['id' => 3, 'name' => 'zar', 'type' => 'konichiwa'],
            ['id' => 4, 'name' => 'tar', 'type' => 'konichiwa'],
        ];

        return [
            [[], 'foo', 'boom', 'boom', []],
            [$array, 'name', 'id', null, ['foo' => 1, 'bar' => 2, 'zar' => 3, 'tar' => 4]],
            [$array, 'name', 'id', 'type', [
                'nihao' => ['foo' => 1, 'bar' => 2],
                'konichiwa' => ['zar' => 3, 'tar' => 4]
            ]]
        ];
    }

    public function testGetColumn()
    {
        $array = [
            [ 'id' => 1, 'profile' => ['name' => 'foo', 'age' => 15], 'created' => '2020/02/19 11:26:35'],
            [ 'id' => 2, 'profile' => ['name' => 'bar', 'age' => 16], 'created' => '2020/02/19 11:26:35']
        ];

        $this->assertEquals(ArrayHelper::getColumn([], 'id'), []);
        $this->assertEquals(ArrayHelper::getColumn($array, null), []);
        $this->assertEquals(ArrayHelper::getColumn($array, 'id'), [1, 2]);
        $this->assertEquals(
            ArrayHelper::getColumn($array, 'id', 'profile.name'),
            [
                ['id' => 1, 'profile.name' => 'foo'],
                ['id' => 2, 'profile.name' => 'bar'],
            ]
        );
        $this->assertEquals(
            ArrayHelper::getColumn($array, function ($item) {
                return $item['id'];
            }),
            [1, 2]
        );
    }

    public function testRemoveWithClosure()
    {
        $array = [
            'foo' => '1',
            'bar' => '1',
            'zar' => '1',
            'tar' => '1',
        ];

        ArrayHelper::remove($array, function (&$arr, $default) {
            foreach ($arr as $i => $val) {
                if ($val === '1') {
                    unset($arr[$i]);
                }
            }
        }, 'boom');

        $this->assertEquals($array, []);
    }

    public function testRemoveAfter()
    {
        $array = ['foo' => 'bar', 'zar'];
        ArrayHelper::remove($array, 'foo');
        $this->assertEquals(['zar'], $array);
    }

    /**
     * @dataProvider removeProvider
     */
    public function testRemove($array, $key, $default, $result)
    {
        $this->assertEquals(ArrayHelper::remove($array, $key, $default), $result);
    }

    public function removeProvider()
    {
        $array = [
            'foo' => 'bar',
            'bar' => [ 'foo' => 'bar', 'zar' => [ 'foo' => 'bar' ] ],
            0 => [ 1 => [ 2 => 'woow' ] ]
        ];

        return [
            [[], 'foo', 'boom', 'boom'],
            [$array, null, 'boom', 'boom'],
            [$array, 'oops', 'boom', 'boom'],
            [$array, 'foo', null, 'bar'],
            [$array, 'bar.foo', null, 'bar'],
            [$array, '0.1.2', null, 'woow'],
            [$array, '0.1.2.3', 'boom', 'boom'],
        ];
    }

    /**
     * @dataProvider hasProvider
     */
    public function testHas($array, $key, $result)
    {
        $this->assertEquals(ArrayHelper::has($array, $key), $result);
    }

    public function hasProvider()
    {
        $array = [
            'foo' => 'bar',
            'bar' => [ 'foo' => 'bar', 'zar' => [ 'foo' => 'bar' ] ],
            0 => [ 1 => [ 2 => 'woow' ] ]
        ];

        return [
            [[], 'foo', false],
            [$array, null, false],
            [$array, 'foo', true],
            [$array, 'oops', false],
            [$array, 'bar.foo', true],
            [$array, '0.1.2', true],
            [$array, 0, true],
            [$array, 1, false],
        ];
    }

    /**
     * @dataProvider getProvider
     */
    public function testGet($array, $key, $default, $result)
    {
        $this->assertEquals(ArrayHelper::get($array, $key, $default), $result);
    }

    public function getProvider()
    {
        $std = new stdClass;
        $std->foo = 'bar';

        $array = [
            'foo' => 'bar',
            'bar' => [
                'foo' => 'bar',
                'zar' => [
                    'foo' => 'bar'
                ]
            ],
            'std' => $std
        ];

        return [
            [$array, function ($arr, $default) { return $arr['foo']; }, 'ko', 'bar'],
            [$array, 'foo', 'ni', 'bar'],
            [$array, 'std.foo', 'chi', 'bar'],
            [$array, 'bar.zar.foo', 'wa', 'bar'],
            [$array, 'tar', 'boom', 'boom'],
            [$array, null, 'boom', 'boom'],
            [[], 'tar', 'boom', 'boom'],
        ];
    }

    public function testMerge()
    {
        $a = ['foo' => 'bar', 'tar' => ['foo', 'bar'], 'foo', 'bar', 'zar'];
        $b = ['foo' => 'zar', 'tar' => ['goo', 'bar'], 'foo', 'zar', 'zar'];

        $this->assertEquals(ArrayHelper::merge($a, $b), [
            'foo' => 'zar',
            'tar' => ['foo', 'bar', 'goo', 'bar'],
            'foo',
            'bar',
            'zar',
            'foo',
            'zar',
            'zar',
        ]);
    }

    /**
     * @dataProvider wrapProvider
     */
    public function testWrap($a, $b)
    {
        $this->assertEquals(ArrayHelper::wrap($a), $b);
    }

    public function wrapProvider()
    {
        return [
            [null, []],
            [[], []],
            [['foo'], ['foo']],
            ['foo', ['foo']],
            [123, [123]],
        ];
    }

    /**
     * @dataProvider toArrayProvider
     */
    public function testToArray($a, $b)
    {
        $this->assertEquals(ArrayHelper::toArray($a), $b);
    }

    public function toArrayProvider()
    {
        $array = [
            'foo' => 'bar',
            'arrayable' => new Arrayable(['foo' => 'bar'])
        ];

        $std = new stdClass;
        $std->foo = 'bar';

        $class = new ObjectClass(['foo' => 'bar']);

        return [
            [['foo' => 'bar'], ['foo' => 'bar']],
            ['foo', ['foo']],
            [$array, ['foo' => 'bar', 'arrayable' => ['foo' => 'bar']]],
            [$std, ['foo' => 'bar']],
            [$class, ['foo' => 'bar']],
        ];
    }
}
