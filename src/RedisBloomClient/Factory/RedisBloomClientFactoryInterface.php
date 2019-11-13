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
use Averias\RedisBloom\DataTypes\DataTypeInterface;
use Averias\RedisBloom\Exception\RedisClientException;

interface RedisBloomClientFactoryInterface
{
    /**
     * @param array $config
     * @return RedisClientAdapterInterface
     * @throws RedisClientException
     */
    public function getAdapter(array $config = []): RedisClientAdapterInterface;

    /**
     * @param array|null $config
     * @return RedisBloomClientInterface
     * @throws RedisClientException
     */
    public function createClient(array $config): RedisBloomClientInterface;

    /**
     * @param string $filterName
     * @param array $config
     * @return BloomFilter
     * @throws RedisClientException
     */
    public function createBloomFilter(string $filterName, array $config = []): DataTypeInterface;
}
