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
 * 容器值对象未找到 (1201)
 *
 * @since 0.2
 */
class ContainerValueNotFoundException extends NotFoundException
{
    /**
     * 代码: 1201
     *
     * @var int
     */
    protected $code = 1201;

    /**
     * 构造
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct(static::formatMessage(
            '未在容器中找到 "{id}".',
            [
                'id' => $id
            ]
        ));
    }
}
