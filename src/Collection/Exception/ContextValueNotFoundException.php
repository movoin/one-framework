<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Collection\Exception
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Collection\Exception;

use One\Exception\NotFoundException;

/**
 * 上下文值未找到 (1202)
 *
 * @since 0.2
 */
class ContextValueNotFoundException extends NotFoundException
{
    /**
     * 代码: 1202
     *
     * @var int
     */
    protected $code = 1202;

    /**
     * 构造
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(static::formatMessage(
            '未在上下文中找到 "{name}".',
            [
                'name' => $name
            ]
        ));
    }
}
