<?php
/**
 * This file is part of the One Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.2
 */

function includeIfExists($file)
{
    return file_exists($file) ? include $file : false;
}

if (! $loader = includeIfExists(__DIR__ . '/vendor/autoload.php')) {
    echo 'You must set up the project dependencies using `composer install`' . PHP_EOL.
        'See https://getcomposer.org/download/ for instructions on installing Composer' . PHP_EOL;
    exit(1);
}

return $loader;
