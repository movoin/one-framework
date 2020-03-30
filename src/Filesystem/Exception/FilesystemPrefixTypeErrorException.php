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
 * 文件系统前缀类型错误异常 (312)
 *
 * @since 0.2
 */
class FilesystemPrefixTypeErrorException extends TypeErrorException
{
    /**
     * 代码: 312
     *
     * @var int
     */
    protected $code = 312;

    /**
     * 构造
     */
    public function __construct()
    {
        parent::__construct('文件系统前缀必须为 "字符串" 且 "不能为空".');
    }
}
