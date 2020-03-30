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
 * 文件系统适配器前缀未定义异常类 (313)
 *
 * @since 0.2
 */
class FilesystemPrefixUndefinedException extends InvalidArgumentException
{
    /**
     * 代码: 313
     *
     * @var int
     */
    protected $code = 313;

    /**
     * 构造
     *
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        parent::__construct(static::formatMessage(
            '文件系统前缀 "{prefix}" 未定义.',
            [
                'prefix' => $prefix
            ]
        ));
    }
}
