<?php
/**
 * @project   phpredis-bloom
 * @author    Rafael Campoy <rafa.campoy@gmail.com>
 * @copyright 2019 Rafael Campoy <rafa.campoy@gmail.com>
 * @license   MIT
 * @link      https://github.com/averias/php-rejson
 *
 * Copyright and license information, is included in
 * the LICENSE file that is distributed with this source code.
 */

namespace Example;

use Averias\RedisBloom\Enum\Connection;
use Averias\RedisBloom\Factory\RedisBloomFactory;

require(dirname(__DIR__).'/vendor/autoload.php');

const EXAMPLE_FILTER = 'example-cms';

const ELEMENTS_INSERTIONS = [
    'black' => 123,
    'white' => 234,
    'green' => 56,
    'red' => 111,
    'blue' => 447,
    'yellow' => 1123,
    'purple' => 1098
];

// create a factory with default connection configuration but pointing to database 15
$factory = new RedisBloomFactory([Connection::DATABASE => 15]);

// create a Count-Min Sketch data type class
$cms = $factory->createCountMinSketch(EXAMPLE_FILTER);

// init CMS
$cms->initByDim(10, 10);

// insert elements with its increment
foreach (ELEMENTS_INSERTIONS as $key => $increment) {
    $cms->incrementBy($key, $increment);
}

$elements = array_keys(ELEMENTS_INSERTIONS);

// query elements
$result = $cms->query(...$elements);

// compare results
foreach (array_values(ELEMENTS_INSERTIONS) as $key => $increment) {
        $elementName = $elements[$key];
        $elementCount = $result[$key];
        $resume = $increment == $elementCount ? 'MATCH!' : 'MISMATCH!';
        echo sprintf(
            "%s element '%s' was incremented by %d, CMS count: %d",
            $resume,
            $elementName,
            $increment,
            $elementCount
        ) . PHP_EOL;
}

$factory->createClient()->del(EXAMPLE_FILTER);
