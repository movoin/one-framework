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

use One\Exception\InvalidArgumentException;

/**
 * 查询路径未定义异常类 (1513)
 *
 * @since 0.2
 */
class FindInPathUndefinedException extends InvalidArgumentException
{
    /**
     * 代码: 1513
     *
     * @var int
     */
    protected $code = 1513;

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('查询路径未定义.');
    }
}
