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

use Averias\RedisBloom\Enum\Connection;
use Averias\RedisBloom\Factory\RedisBloomFactory;

require(dirname(__DIR__) . '/vendor/autoload.php');

function boolToString($value, $message)
{
    $result = $value ? 'OK!' : 'FAILED!';
    echo sprintf("%s: %s", $message, $result) . PHP_EOL;
}

$factory = new RedisBloomFactory([Connection::DATABASE => 15]);

$bf = $factory->createBloomFilter('bf-key');
$bf->reserve(0.01, 100);

echo PHP_EOL . '*** Bloom Filter' . PHP_EOL;
boolToString($bf->add(12), "inserted 12 as integer?: ");
boolToString($bf->add('12'), "inserted 12 as string?: ");
boolToString($bf->exists(12), "exists 12 as integer?: ");
boolToString($bf->exists('12'), "exists 12 as string?: ");

$cf = $factory->createCuckooFilter('cf-key');
$cf->reserve(100);

echo PHP_EOL . '*** Cuckoo Filter' . PHP_EOL;
boolToString($cf->add(14.5), "inserted 12 as float?: ");
boolToString($cf->add('14.5'), "inserted 12 as string?: ");
boolToString($cf->exists(14.5), "exists 12 as float?: ");
boolToString($cf->exists('14.5'), "exists 12 as string?: ");

echo sprintf("occurrences of 14.5 as float %d", $cf->count(14.5)) . PHP_EOL;
echo sprintf("occurrences of 14.5 as string %d", $cf->count('14.5')) . PHP_EOL;
boolToString($cf->delete(14.5), "deleted 14.5 as float?: ");
boolToString($cf->delete('14.5'), "deleted 14.5 as string?: ");
echo sprintf("occurrences of 14.5 as float %d", $cf->count(14.5)) . PHP_EOL;
echo sprintf("occurrences of 14.5 as string %d", $cf->count('14.5')) . PHP_EOL;

$cms = $factory->createCountMinSketch('cms-key');
$cms->initByDim(100, 7);

echo PHP_EOL . '*** Count-Min Sketch' . PHP_EOL;
boolToString($cms->incrementBy(78, 5), "incremented item 78 as integer by 5?: ");
boolToString($cms->incrementBy('78', 3), "incremented item 78 as string by 3?: ");
echo "count of 78: " . $cms->query(78)[0] . PHP_EOL;

$tk = $factory->createTopK('tk-key');
$tk->reserve(2, 100, 7, 0.95);

echo PHP_EOL . '*** Top-k' . PHP_EOL;
echo 'incremented item  6.69 as float by 15 adn as string by 7' . PHP_EOL;
$tk->incrementBy(6.69, 15, '6.69', 7);
echo "count of 6.69: " . $tk->count(6.69)[0] . PHP_EOL;

$client = $factory->createClient();
$client->del('cf-key', 'bf-key', 'cms-key', 'tk-key');
