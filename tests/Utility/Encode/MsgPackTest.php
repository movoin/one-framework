<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Utility\Encode
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Utility\Encode;

use MessagePack\PackOptions;
use One\Utility\Encode\MsgPack;

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
     * @expectedException One\Utility\Encode\Exception\EncodeException
     */
    public function testPackInvalidOptionException()
    {
        MsgPack::pack('test', PackOptions::FORCE_STR | PackOptions::FORCE_BIN);
    }

    /**
     * @expectedException One\Utility\Encode\Exception\EncodeException
     */
    public function testPackingFailedException()
    {
        MsgPack::pack(new \stdClass());
    }

    /**
     * @expectedException One\Utility\Encode\Exception\EncodeException
     */
    public function testUnPackInvalidOptionException()
    {
        MsgPack::unpack(MsgPack::pack('foobar'), PackOptions::FORCE_STR | PackOptions::FORCE_BIN);
    }

    /**
     * @expectedException One\Utility\Encode\Exception\EncodeException
     */
    public function testUnpackingFailedException()
    {
        MsgPack::unpack("\xc1");
    }
}
