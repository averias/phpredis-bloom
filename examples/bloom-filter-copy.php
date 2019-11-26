<?php
/**
 * @project   phpredis-bloom
 * @author    Rafael Campoy <rafa.campoy@gmail.com>
 * @copyright 2019 Rafael Campoy <rafa.campoy@gmail.com>
 * @license   MIT
 * @link      https://github.com/averias/phpredis-bloom
 *
 * Copyright and license information, is included in
 * the LICENSE file that is distributed with this source code.
 */

namespace Example;

require(dirname(__DIR__) . '/vendor/autoload.php');

use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Factory\RedisBloomFactory;

const SCAN_KEY = 'scan-key';
const TARGET_SCAN_KEY = 'target-scan-key';

$factory = new RedisBloomFactory();
$client = $factory->createClient();

$capacity = 10000;
$insertData = [];


for ($i = 0; $i < $capacity; $i++) {
    $insertData[] = "abc{$i}";
}
$client->bloomFilterInsert(
    SCAN_KEY,
    $insertData,
    [OptionalParams::CAPACITY => $capacity, OptionalParams::ERROR => 0.01]
);

$bloomFilter = $factory->createBloomFilter(SCAN_KEY);
$bloomFilter->copy(TARGET_SCAN_KEY);

$multiExists = $client->bloomFilterMultiExists(TARGET_SCAN_KEY, ...$insertData);

$nonExistentKey = [];
foreach ($multiExists as $key => $exist) {
    if ($exist === false) {
        $nonExistentKey[] = $insertData[$key];
    }
}

if (empty($nonExistentKey)) {
    echo "all items were copied" . PHP_EOL;
} else {
    echo count($nonExistentKey) . " were not copied:" . PHP_EOL;
    var_dump($nonExistentKey);
}

$client->del(SCAN_KEY);
$client->del(TARGET_SCAN_KEY);
