<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Utility
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Utility;

use One\Utility\Assert;

class AssertTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideTestTrue
     */
    public function testTrue($method, $value)
    {
        $this->assertTrue(Assert::$method($value));
    }

    public function provideTestTrue()
    {
        return [
            [ 'string',           'foo' ],
            [ 'stringNotEmpty',   'foo' ],
            [ 'integer',          100 ],
            [ 'float',            1.1 ],
            [ 'numeric',          '10123ABA' ],
            [ 'natural',          99 ],
            [ 'boolean',          true ],
            [ 'object',           new \stdClass ],
            [ 'callable',         function () { return true; } ],
            [ 'array',            ['foo'] ],
            [ 'ip',               '127.0.0.1' ],
            [ 'url',              'http://domain.com' ],
            [ 'json',             '{"foo": "bar"}' ],
            [ 'namespace',        'foo\\bar\\zar' ],
            [ 'datetime',         new \DateTime ],
            [ 'datetime',         1535423356248 ]
        ];
    }

    /**
     * @dataProvider provideTestFalse
     */
    public function testFalse($method, $value)
    {
        $this->assertFalse(Assert::$method($value));
    }

    public function provideTestFalse()
    {
        return [
            [ 'string',           1 ],
            [ 'stringNotEmpty',   '' ],
            [ 'stringNotEmpty',   1 ],
            [ 'integer',          '100' ],
            [ 'float',            1 ],
            [ 'numeric',          'hello+' ],
            [ 'natural',          '99' ],
            [ 'boolean',          'true' ],
            [ 'object',           'new \stdClass' ],
            [ 'callable',         'function () { return true; }' ],
            [ 'array',            'foo' ],
            [ 'ip',               'localhost' ],
            [ 'url',              'vvvom' ],
            [ 'json',             "{'foo': 'bar'}" ],
            [ 'namespace',        'foobar' ],
            [ 'datetime',         0 ]
        ];
    }

    public function testResource()
    {
        $tmp = tmpfile();
        $this->assertTrue(Assert::resource($tmp));
        fclose($tmp);

        $tmp = tmpfile();
        $this->assertTrue(Assert::resource($tmp, 'stream'));
        fclose($tmp);
    }

    public function testCountable()
    {
        $this->assertTrue(Assert::countable([]));
        $this->assertTrue(Assert::countable(new \One\Collection\Collection));
    }

    public function testIterable()
    {
        $this->assertTrue(Assert::iterable([]));
        $this->assertTrue(Assert::iterable(new \One\Collection\Collection));
    }

    public function testInstanceOf()
    {
        $this->assertTrue(Assert::instanceOf(new \One\Collection\Collection, '\\One\\Collection\\Contract\\Arrayable'));
    }

    public function testInstanceOfAny()
    {
        $this->assertTrue(Assert::instanceOfAny(
            new \One\Collection\Collection,
            [
                '\\One\\Collection\\Contract\\Arrayable'
            ]
        ));
        $this->assertFalse(Assert::instanceOfAny(
            new \One\Collection\Collection,
            [
                '\\Exception'
            ]
        ));
    }

    public function testRange()
    {
        $this->assertTrue(Assert::range(10, 0, 100));
    }

    public function testOneOf()
    {
        $this->assertTrue(Assert::oneOf('foo', ['tar', 'zar', 'bar', 'foo']));
    }

    public function testContains()
    {
        $this->assertTrue(Assert::contains('hello world', 'or'));
    }

    public function testEmail()
    {
        $this->assertTrue(Assert::email('movoin@gmail.com'));
        $this->assertTrue(Assert::email('movoin@gmail.com', 'gmail.com'));

        $this->assertFalse(Assert::email('movoin[at]gmail.com'));
        $this->assertFalse(Assert::email('movoin@gmail.com', 'qq.com'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(Assert::startsWith('@cmd', '@'));
        $this->assertFalse(Assert::startsWith('#cmd', '@'));
    }

    public function testMobile()
    {
        //(18[0-9])|(19[1|3|8|9]))\\d{8}$/i', $value);
        // 13X
        foreach (range(0, 9) as $x) {
            $this->assertTrue(Assert::mobile('13' . $x . '00000000'));
        }
        // 14X
        $this->assertTrue(Assert::mobile('14500000000'));
        $this->assertTrue(Assert::mobile('14700000000'));
        // 15X
        foreach (range(0, 3) as $x) {
            $this->assertTrue(Assert::mobile('15' . $x . '00000000'));
        }
        foreach (range(5, 9) as $x) {
            $this->assertTrue(Assert::mobile('15' . $x . '00000000'));
        }
        // 16X
        $this->assertTrue(Assert::mobile('16600000000'));
        // 17X
        foreach (range(2, 3) as $x) {
            $this->assertTrue(Assert::mobile('17' . $x . '00000000'));
        }
        foreach (range(5, 8) as $x) {
            $this->assertTrue(Assert::mobile('17' . $x . '00000000'));
        }
        // 18X
        foreach (range(0, 9) as $x) {
            $this->assertTrue(Assert::mobile('18' . $x . '00000000'));
        }
        // 19X
        $this->assertTrue(Assert::mobile('19100000000'));
        $this->assertTrue(Assert::mobile('19300000000'));
        $this->assertTrue(Assert::mobile('19800000000'));
        $this->assertTrue(Assert::mobile('19900000000'));
    }

    public function testPhone()
    {
        $this->assertTrue(Assert::phone('0760-88888888'));
    }

    /**
     * @dataProvider numberProvider
     */
    public function testNumber($test)
    {
        $this->assertTrue(Assert::number($test));
    }

    public function numberProvider()
    {
        return [
            ['123'],
            ['1.23'],
            ['-123'],
            [0],
            [123],
            [-123],
            [-1.43],
            [0x155],
            [1e7],
            ['1e7'],
            ['.24'],
        ];
    }

    /**
     * @dataProvider notNumberProvider
     */
    public function testNotNumber($test)
    {
        $this->assertFalse(Assert::number($test));
    }

    public function notNumberProvider()
    {
        return [
            ['a123'],
            ['a1.23'],
            ['1-123']
        ];
    }
}
