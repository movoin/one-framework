<?php
declare(strict_types=1);
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Filesystem\Iterator
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

namespace One\Tests\Filesystem\Iterator;

use One\Filesystem\Iterator\RecursiveDirectoryIterator;
use One\Filesystem\Iterator\ExcludeDirectoryFilterIterator;

class ExcludeDirectoryFilterIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testDirectory()
    {
        $iterator = $this->getIterator(['Fixtures']);

        foreach ($iterator as $file) {
            $this->assertTrue($file->getFilename() !== 'Fixtures');
        }
    }

    protected function getIterator(
        array $excludes,
        string $directory = __DIR__ . '/../../../'
    ) {
        return new ExcludeDirectoryFilterIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            $excludes
        );
    }
}
