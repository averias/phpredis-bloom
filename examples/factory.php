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

require(dirname(__DIR__).'/vendor/autoload.php');

const EXAMPLE_FILTER = 'example-filter';

/**
 * Default connection params:
 * [
 *     'host' => '127.0.0.1',
 *     'port' => 6379,
 *     'timeout' => 0.0, // seconds
 *     'retryInterval' => 15 // milliseconds
 *     'readTimeout' => 2, // seconds
 *     'persistenceId' => null // string for persistent connections, null for no persistent ones
 *     'database' => 0 // Redis database index [0..15]
 * ]
 *
 * you can create a factory with default connection configuration by not passing any param in the constructor
 * $defaultFactory = new RedisBloomFactory();
 */

// create a factory with default connection configuration but pointing to database 15
$factoryDB15 = new RedisBloomFactory([Connection::DATABASE => 15]);

// it creates a RedisBloomClient with same default connection configuration as specified in factory above
$clientDB15 = $factoryDB15->createClient();

// using the same factory you can create a BloomFilter object pointing to database 14 and filter name = 'example-filter'
$bloomFilterDB14 = $factoryDB15->createBloomFilter(EXAMPLE_FILTER, [Connection::DATABASE => 14]);

// add 'item-15' to 'example-filter' bloom filter on database 15
$clientDB15->bloomFilterAdd(EXAMPLE_FILTER, 'item-15');

// add 'item-14' to 'example-filter' bloom filter on database 14
$bloomFilterDB14->add('item-14');

// disconnect
$bloomFilterDB14->disconnect();

// create another RedisBloomClient pointing to database 14
$clientDB14 = $factoryDB15->createClient([Connection::DATABASE => 14]);

$clientDB15->bloomFilterExists(EXAMPLE_FILTER, 'database15'); //true
$clientDB15->bloomFilterExists(EXAMPLE_FILTER, 'database14'); // false

$clientDB14->bloomFilterExists(EXAMPLE_FILTER, 'database15'); // false
$clientDB14->bloomFilterExists(EXAMPLE_FILTER, 'database14'); // true

// delete bloom filter on database 15
$clientDB15->executeRawCommand('DEL', EXAMPLE_FILTER);

// delete bloom filter on database 14
$clientDB14->del(EXAMPLE_FILTER);

// disconnect

$clientDB15->disconnect();
$clientDB14->disconnect();

// automatic reconnection
$bloomFilterDB14->add('reconnected');
$bloomFilterDB14->exists('reconnected'); //true
$bloomFilterDB14->disconnect();
