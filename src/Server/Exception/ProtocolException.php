<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Server\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Server\Exception;

/**
 * 协议异常类
 *
 * @since 0.2
 */
class ProtocolException extends ServerException
{
    /**
     * 协议名称
     *
     * @var string
     */
    private $protocolName;
}
