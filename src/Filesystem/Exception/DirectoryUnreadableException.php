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

use One\Exception\LogicException;

/**
 * 目录不可读异常类 (1503)
 *
 * @since 0.2
 */
class DirectoryUnreadableException extends LogicException
{
    /**
     * 代码: 1503
     *
     * @var int
     */
    protected $code = 1503;

    /**
     * 构造
     *
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        parent::__construct(static::formatMessage(
            '目录 "{dir}" 不可读.',
            [
                'dir' => $dir
            ]
        ));
    }
}
