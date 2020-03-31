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
 * 文件读取失败异常 (1506)
 *
 * @since 0.2
 */
class FileReadFailureException extends LogicException
{
    /**
     * 代码: 1506
     *
     * @var int
     */
    protected $code = 1506;

    /**
     * 构造
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct(static::formatMessage(
            '文件 "{file}" 读取失败.',
            [
                'file' => $file
            ]
        ));
    }
}
