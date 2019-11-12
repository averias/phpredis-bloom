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

namespace Averias\RedisBloom\Factory;

use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\DataTypes\BloomFilter;

interface RedisBloomClientFactoryInterface
{
    public function getAdapter(array $config = []): RedisClientAdapterInterface;
    public function createClient(array $config): RedisBloomClientInterface;
    public function createBloomFilter(string $filterName, array $config = []): BloomFilter;
}
