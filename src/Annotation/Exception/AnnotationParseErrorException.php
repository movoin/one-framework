<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Annotation\Exception;

use One\Exception\ParseErrorException;

/**
 * 注释解析错误异常类 (100)
 *
 * @since 0.2
 */
class AnnotationParseErrorException extends ParseErrorException
{
    /**
     * 代码: 100
     *
     * @var int
     */
    protected $code = 100;

    /**
     * 构造
     *
     * @param string $value
     * @param string $suggest
     * @param array $extras
     */
    public function __construct(string $value, string $suggest = null, array $extras = [])
    {
        $message = '注释 "{value}" 解析错误.';

        if (null !== $suggest) {
            $message .= ', ' . $suggest;
        }
        $message .= '.';

        $extras['value'] = $value;

        parent::__construct(static::formatMessage($message, $extras));
    }
}
