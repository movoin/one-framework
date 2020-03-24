<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Collection\Concern
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Collection\Concern;

use One\Collection\Collection;
use One\Exception\RuntimeException;

/**
 * 集合容器特征
 *
 * @since 0.2
 */
trait HasCollection
{
    /**
     * 集合容器
     *
     * @var \One\Collection\Collection
     */
    private $coll;

    /**
     * 设置容器
     *
     * @param \One\Collection\Collection $coll
     */
    public function setCollection(Collection $coll): void
    {
        $this->coll = $coll;
    }

    /**
     * 获得容器
     *
     * @return \One\Collection\Collection
     */
    public function getCollection(): Collection
    {
        if ($this->coll === null) {
            $this->coll = new Collection;
        }

        return $this->coll;
    }
}
