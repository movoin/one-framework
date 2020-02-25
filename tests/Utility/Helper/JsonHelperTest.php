<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Utility\Helper
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Utility\Helper;

use One\Utility\Helper\JsonHelper;

class JsonHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testEncode()
    {
        $json = '{"foo":"bar","int":1,"float":1.1,"unicode":"中文"}';

        $this->assertEquals($json, JsonHelper::encode([
            'foo' => 'bar',
            'int' => 1,
            'float' => 1.1,
            'unicode' => '中文'
        ]));
    }

    /**
     * @expectedException One\Utility\Exception\JsonException
     */
    public function testEncodeException()
    {
        JsonHelper::encode("\xB1\x31");
    }

    public function testDecode()
    {
        $array = [
            'foo' => 'bar',
            'int' => 1,
            'float' => 1.1,
            'unicode' => '中文'
        ];

        $this->assertEquals(JsonHelper::decode('{"foo":"bar","int":1,"float":1.1,"unicode":"中文"}'), $array);
    }

    /**
     * @expectedException One\Utility\Exception\JsonException
     */
    public function testDecodeException()
    {
        JsonHelper::decode("{'foo': 'bar'}");
    }
}
