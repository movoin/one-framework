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
 * 文件不存在异常类 (306)
 */
class FileNotExistsException extends NotExistsException
{
    /**
     * 代码: 306
     *
     * @var int
     */
    protected $code = 306;

    /**
     * 构造
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        parent::__construct(static::formatMessage(
            '文件 "{file}" 不存在.',
            [
                'file' => $file
            ]
        ));
    }
}
