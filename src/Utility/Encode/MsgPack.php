<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Utility\Encode
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Utility\Encode;

use MessagePack\Packer;
use MessagePack\BufferUnpacker;
use MessagePack\PackOptions;
use MessagePack\Exception\InvalidOptionException;
use MessagePack\Exception\PackingFailedException;
use MessagePack\Exception\UnpackingFailedException;
use One\Utility\Encode\Exception\EncodeException;

/**
 * MessagePack 编码类
 *
 * @since 0.2
 */
class MsgPack
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * MessagePack 编码
     *
     * @static
     *
     * @param mixed $value
     * @param \MessagePack\PackOptions|int|null $options
     *
     * @return string
     * @throws \One\Utility\Encode\Exception\EncodeException
     */
    public static function pack($value, $options = null): string
    {
        try {
            $packer = new Packer($options);
            $data = $packer->pack($value);
        } catch (InvalidOptionException $e) {
            throw new EncodeException('MsgPack', $e->getMessage(), $e->getCode(), $e);
        } catch (PackingFailedException $e) {
            throw new EncodeException('MsgPack', $e->getMessage(), $e->getCode(), $e);
        }

        unset($packer);

        return $data;
    }

    /**
     * MessagePack 解码
     *
     * @static
     *
     * @param string $data
     * @param \MessagePack\PackOptions|int|null $options
     *
     * @return mixed
     * @throws \One\Utility\Encode\Exception\EncodeException
     */
    public static function unpack(string $data, $options = null)
    {
        try {
            $unpacker = new BufferUnpacker($data, $options);
            $unpacked = $unpacker->unpack();
        } catch (InvalidOptionException $e) {
            throw new EncodeException('MsgPack', $e->getMessage(), $e->getCode(), $e);
        } catch (UnpackingFailedException $e) {
            throw new EncodeException('MsgPack', $e->getMessage(), $e->getCode(), $e);
        }

        unset($unpacker);

        return $unpacked;
    }
}
