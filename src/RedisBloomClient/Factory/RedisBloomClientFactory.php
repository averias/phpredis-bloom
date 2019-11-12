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
use Averias\RedisBloom\Client\RedisBloomClient;
use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\DataTypes\BloomFilter;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Validator\RedisClientValidator;
use Averias\RedisBloom\Adapter\AdapterProvider;
use Exception;

class RedisBloomClientFactory implements RedisBloomClientFactoryInterface
{
    /** @var AdapterProvider */
    protected $adapterProvider;

    public function __construct()
    {
        $this->adapterProvider = new AdapterProvider(new RedisClientValidator());
    }

    /**
     * @param array $config
     * @return RedisClientAdapterInterface
     * @throws RedisClientException
     */
    public function getAdapter(array $config = []): RedisClientAdapterInterface
    {
        try {
            $adapter = $this->adapterProvider->getRedisClientAdapter($config);
        } catch (Exception $e) {
            throw new RedisClientException($e->getMessage());
        }

        return $adapter;
    }

    /**
     * @param array|null $config
     * @return RedisBloomClientInterface
     * @throws RedisClientException
     */
    public function createClient(array $config = []): RedisBloomClientInterface
    {
        return new RedisBloomClient($this->getAdapter($config));
    }

    /**
     * @param string $filterName
     * @param array $config
     * @return BloomFilter
     * @throws RedisClientException
     */
    public function createBloomFilter(string $filterName, array $config = []): BloomFilter
    {
        return new BloomFilter($filterName, $this->getAdapter($config));
    }
}
