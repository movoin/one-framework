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

use One\Exception\NotExistsException;

/**
 * 文件已存在异常类 (1504)
 */
class FileExistsException extends NotExistsException
{
    /**
     * 代码: 1504
     *
     * @var int
     */
    protected $code = 1504;

    /**
     * 构造
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct(static::formatMessage(
            '文件 "{file}" 已存在.',
            [
                'file' => $file
            ]
        ));
    }
}
