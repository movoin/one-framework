<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Encoder
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Encoder;

use MessagePack\Packer;
use MessagePack\BufferUnpacker;
use MessagePack\PackOptions;
use MessagePack\Exception\InvalidOptionException;
use MessagePack\Exception\PackingFailedException;
use MessagePack\Exception\UnpackingFailedException;
use One\Encoder\Exception\DecodeException;
use One\Encoder\Exception\EncodeException;

/**
 * MessagePack 编码类
 *
 * @since 0.2
 */
class MsgPack
{
    public const FORCE_STR = PackOptions::FORCE_STR;
    public const FORCE_BIN = PackOptions::FORCE_BIN;
    public const DETECT_STR_BIN = PackOptions::DETECT_STR_BIN;
    public const FORCE_ARR = PackOptions::FORCE_ARR;
    public const FORCE_MAP = PackOptions::FORCE_MAP;
    public const DETECT_ARR_MAP = PackOptions::DETECT_ARR_MAP;
    public const FORCE_FLOAT32 = PackOptions::FORCE_FLOAT32;
    public const FORCE_FLOAT64 = PackOptions::FORCE_FLOAT64;

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
     * @param int|null $options
     *
     * @return string
     * @throws \One\Encoder\Exception\EncodeException
     */
    public static function pack($value, ?int $options = null): string
    {
        try {
            $packer = new Packer($options);
            $data = $packer->pack($value);
        } catch (InvalidOptionException $e) {
            throw new EncodeException('MsgPack', $e->getMessage(), $e);
        } catch (PackingFailedException $e) {
            throw new EncodeException('MsgPack', $e->getMessage(), $e);
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
     * @param int|null $options
     *
     * @return mixed
     * @throws \One\Encoder\Exception\DecodeException
     */
    public static function unpack(string $data, ?int $options = null)
    {
        try {
            $unpacker = new BufferUnpacker($data, $options);
            $unpacked = $unpacker->unpack();
        } catch (InvalidOptionException $e) {
            throw new DecodeException('MsgPack', $e->getMessage(), $e);
        } catch (UnpackingFailedException $e) {
            throw new DecodeException('MsgPack', $e->getMessage(), $e);
        }

        unset($unpacker);

        return $unpacked;
    }
}
