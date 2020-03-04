<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Encode
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Encode;

use One\Encode\Json;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    public function testEncode()
    {
        $json = '{"foo":"bar","int":1,"float":1.1,"unicode":"中文"}';

        $this->assertEquals($json, Json::encode([
            'foo' => 'bar',
            'int' => 1,
            'float' => 1.1,
            'unicode' => '中文'
        ]));
    }

    /**
     * @expectedException One\Encode\Exception\EncodeException
     */
    public function testEncodeException()
    {
        Json::encode("\xB1\x31");
    }

    public function testDecode()
    {
        $array = [
            'foo' => 'bar',
            'int' => 1,
            'float' => 1.1,
            'unicode' => '中文'
        ];

        $this->assertEquals(Json::decode('{"foo":"bar","int":1,"float":1.1,"unicode":"中文"}'), $array);
    }

    /**
     * @expectedException One\Encode\Exception\EncodeException
     */
    public function testDecodeException()
    {
        Json::decode("{'foo': 'bar'}");
    }

    public function testReadFile()
    {
        $array = ['foo' => 'bar'];

        $this->assertEquals(Json::readFile(__DIR__ . '/Fixtures/test.json'), $array);
    }

    /**
     * @expectedException One\Encode\Exception\EncodeException
     */
    public function testReadFileException()
    {
        Json::readFile("path/to/file");
    }
}
