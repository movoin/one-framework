<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Utility\Concern
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Utility\Concern;

use One\Exception\RuntimeException;

trait HasGetter
{
    /**
     * __get()
     *
     * @param string $name
     *
     * @return mixed
     * @throws \One\Exception\RuntimeException
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists(get_parent_class(), '__get')) {
            return parent::__get($name);
        }

        unset($getter);

        throw new RuntimeException("未定义属性: `{$name}`");
    }
}
