<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Collection
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Collection;

use One\Collection\Concern\HasCollection;
use One\Collection\Concern\HasContainer;
use One\Collection\Concern\HasContext;
use One\Collection\Collection;
use One\Collection\Container;
use One\Collection\Context;

class ConcernTest extends \PHPUnit\Framework\TestCase
{
    use HasCollection;
    use HasContainer;
    use HasContext;

    /**
     * @dataProvider allProvider
     */
    public function testAll($name, $object)
    {
        call_user_func_array([$this, 'set' . $name], [$object]);

        $this->assertInstanceOf(get_class($object), call_user_func_array([$this, 'get' . $name], []));
    }

    public function allProvider()
    {
        return [
            ['Collection', new Collection],
            ['Container', new Container],
            ['Context', new Context],
        ];
    }
}
