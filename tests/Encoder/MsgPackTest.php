<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Encoder
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Encoder;

use One\Encoder\MsgPack;

class MsgPackTest extends \PHPUnit\Framework\TestCase
{
    public function testPack()
    {
        $data = ['foo' => '中文'];

        $this->assertEquals(
            $data,
            MsgPack::unpack(
                MsgPack::pack($data)
            )
        );
    }

    /**
     * @expectedException One\Encoder\Exception\EncodeException
     */
    public function testPackInvalidOptionException()
    {
        MsgPack::pack('test', MsgPack::FORCE_STR | MsgPack::FORCE_BIN);
    }

    /**
     * @expectedException One\Encoder\Exception\EncodeException
     */
    public function testPackingFailedException()
    {
        MsgPack::pack(new \stdClass());
    }

    /**
     * @expectedException One\Encoder\Exception\DecodeException
     */
    public function testUnPackInvalidOptionException()
    {
        MsgPack::unpack(MsgPack::pack('foobar'), MsgPack::FORCE_STR | MsgPack::FORCE_BIN);
    }

    /**
     * @expectedException One\Encoder\Exception\DecodeException
     */
    public function testUnpackingFailedException()
    {
        MsgPack::unpack("\xc1");
    }
}
