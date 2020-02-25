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

use One\Utility\Exception\EncodeException;
use One\Utility\Helper\EncodeHelper;

class EncodeHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider base62Provider
     */
    public function testBase62($data, $type)
    {
        $encode = EncodeHelper::encodeBase62($data);
        $this->assertEquals($data, EncodeHelper::decodeBase62($encode, $type));
    }

    public function base62Provider()
    {
        return [
            ['', EncodeHelper::TYPE_STRING],
            ['string', EncodeHelper::TYPE_STRING],
            ["\x00\x00\x00\x01\x02", EncodeHelper::TYPE_STRING],
            [123456, EncodeHelper::TYPE_INT],
        ];
    }

    public function testEncodeBase62Exception()
    {
        $this->expectException(EncodeException::class);
        EncodeHelper::encodeBase62([]);
    }

    public function testDecodeBase62Exception()
    {
        $this->expectException(EncodeException::class);
        EncodeHelper::decodeBase62('ababab', 3);
    }

    public function testValidateBase62Exception()
    {
        $this->expectException(EncodeException::class);
        EncodeHelper::decodeBase62('-07d8e31da269bf28');
    }
}
