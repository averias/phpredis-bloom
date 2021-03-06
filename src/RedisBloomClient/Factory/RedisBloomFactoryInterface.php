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

namespace Averias\RedisBloom\Factory;

use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\DataTypes\BloomFilterInterface;
use Averias\RedisBloom\DataTypes\CountMinSketchInterface;
use Averias\RedisBloom\DataTypes\CuckooFilterInterface;
use Averias\RedisBloom\DataTypes\TopKInterface;
use Averias\RedisBloom\Exception\RedisClientException;

interface RedisBloomFactoryInterface
{
    /**
     * @param array|null $config
     * @return RedisClientAdapterInterface
     * @throws RedisClientException
     */
    public function getAdapter(?array $config = null): RedisClientAdapterInterface;

    /**
     * @param array|null $config
     * @return RedisBloomClientInterface
     * @throws RedisClientException
     */
    public function createClient(?array $config = null): RedisBloomClientInterface;

    /**
     * @param string $filterName
     * @param array|null $config
     * @return BloomFilterInterface
     * @throws RedisClientException
     */
    public function createBloomFilter(string $filterName, ?array $config = null): BloomFilterInterface;

    /**
     * @param string $filterName
     * @param array|null $config
     * @return CuckooFilterInterface
     * @throws RedisClientException
     */
    public function createCuckooFilter(string $filterName, ?array $config = null): CuckooFilterInterface;

    /**
     * @param string $filterName
     * @param array|null $config
     * @return TopKInterface
     * @throws RedisClientException
     */
    public function createTopK(string $filterName, ?array $config = null): TopKInterface;

    /**
     * @param string $filterName
     * @param array|null $config
     * @return CountMinSketchInterface
     * @throws RedisClientException
     */
    public function createCountMinSketch(string $filterName, ?array $config = null): CountMinSketchInterface;
}
