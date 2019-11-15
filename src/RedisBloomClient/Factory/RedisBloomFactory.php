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
use Averias\RedisBloom\Client\RedisBloomClient;
use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\DataTypes\BloomFilter;
use Averias\RedisBloom\DataTypes\DataTypeInterface;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Validator\RedisClientValidator;
use Averias\RedisBloom\Adapter\AdapterProvider;
use Exception;

class RedisBloomFactory implements RedisBloomFactoryInterface
{
    /** @var array */
    protected $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config;
    }

    /**
     * @param array|null $config
     * @return RedisClientAdapterInterface
     * @throws RedisClientException
     */
    public function getAdapter(?array $config = null): RedisClientAdapterInterface
    {
        try {
            $config = $config ?? $this->config;
            $adapter = $this->getAdapterProvider()->get($config);
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
    public function createClient(?array $config = null): RedisBloomClientInterface
    {
        return new RedisBloomClient($this->getAdapter($config));
    }

    /**
     * @param string $filterName
     * @param array|null $config
     * @return BloomFilter
     * @throws RedisClientException
     */
    public function createBloomFilter(string $filterName, ?array $config = null): DataTypeInterface
    {
        return new BloomFilter($filterName, $this->getAdapter($config));
    }

    /**
     * @return AdapterProvider
     */
    protected function getAdapterProvider(): AdapterProvider
    {
        return new AdapterProvider(new RedisClientValidator());
    }
}
