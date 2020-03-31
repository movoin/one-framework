<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Filesystem\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Filesystem\Exception;

use One\Exception\TypeErrorException;

/**
 * Rewind 操作对象类型错误异常类 (1514)
 *
 * @since 0.2
 */
class RewindResourceTypeErrorException extends TypeErrorException
{
    /**
     * 代码: 1514
     *
     * @var int
     */
    protected $code = 1514;

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('rewind 操作对象必须为 "resource" 类型.');
    }
}
