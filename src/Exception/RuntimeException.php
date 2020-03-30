<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Exception;

use One\Exception\Exception;

/**
 * 运行时异常基类
 *
 * @since 0.2
 */
class RuntimeException extends Exception
{
    /**
     * 类型: 运行时
     *
     * @var int
     */
    protected $type = self::RUNTIME;
}
