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
 * 文件系统路径超出目录范围异常 (310)
 *
 * @since 0.2
 */
class FilesystemPathOutOfRangeException extends LogicException
{
    /**
     * 代码: 310
     *
     * @var int
     */
    protected $code = 310;

    /**
     * 构造
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(static::formatMessage(
            '文件系统路径 "{path}" 已超出根目录范围.',
            [
                'path' => $path
            ]
        ));
    }
}
