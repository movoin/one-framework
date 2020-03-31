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
 * 文件系统路径类型错误异常 (1510)
 *
 * @since 0.2
 */
class FilesystemPathTypeErrorException extends TypeErrorException
{
    /**
     * 代码: 1510
     *
     * @var int
     */
    protected $code = 1510;

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('文件系统路径必须为 "字符串" 且 "不能为空".');
    }
}
