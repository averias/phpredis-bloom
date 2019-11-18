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

// create a factory with default connection configuration but pointing to database 15
$factory = new RedisBloomFactory([Connection::DATABASE => 15]);

// create several Count-Min Sketch data type classes with same width and depth values and insert elements
$cms1 = $factory->createCountMinSketch('key-merge1');
$cms1->initByDim(10, 10);
$cms1->incrementBy('blue', 10, 'red', 20, 'yellow', 30);

$cms2 = $factory->createCountMinSketch('key-merge2');
$cms2->initByDim(10, 10);
$cms2->incrementBy('blue', 10, 'red', 20, 'yellow', 30);

$cms3 = $factory->createCountMinSketch('key-merge3');
$cms3->initByDim(10, 10);
$cms3->incrementBy('blue', 10, 'red', 20, 'yellow', 30);


$targetCms = $factory->createCountMinSketch('key-merge4');
$targetCms->initByDim(10, 10);

// multiply by 2 all elements in 'key-merge1', by 3 all elements in 'key-merge2' and by 4 all elements in 'key-merge3'
$targetCms->mergeFrom(3, ['key-merge1', 'key-merge2', 'key-merge3'], [2, 3, 4]);

$result = $targetCms->query('blue', 'red', 'yellow');
var_dump($result);

/**
 * result = [90, 180, 270]
 *
 * count of 'blue' = 90 =>  2*10 + 3*10 + 4*10 = 90
 * count of 'red' = 180 =>  2*20 + 3*20 + 4*20 = 180
 * count of 'yellow' = 270 =>  2*30 + 3*30 + 4*30 = 270
 */

$client = $factory->createClient();
$client->del('key-merge1', 'key-merge2', 'key-merge3', 'key-merge4');
