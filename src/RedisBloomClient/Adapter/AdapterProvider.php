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

namespace Averias\RedisBloom\Adapter;

use Averias\RedisBloom\Connection\ConnectionOptions;
use Averias\RedisBloom\Exception\ConnectionException;
use Averias\RedisBloom\Exception\RedisBloomModuleNotInstalledException;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Validator\RedisClientValidatorInterface;
use Redis;

class AdapterProvider
{
    /** @var RedisClientValidatorInterface */
    protected $validator;

    public function __construct(RedisClientValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array|null $config
     * @return RedisClientAdapterInterface
     * @throws ConnectionException
     * @throws RedisBloomModuleNotInstalledException
     * @throws ResponseException
     */
    public function get(?array $config = null): RedisClientAdapterInterface
    {
        $redisClientAdapter = $this->getRedisClientAdapter($config);
        $this->validateRedisBloomModuleInstalled($redisClientAdapter);

        return $redisClientAdapter;
    }

    /**
     * @param array|null $config
     * @return RedisAdapterInterface
     * @throws ConnectionException
     */
    protected function getRedisAdapter(?array $config = null): RedisAdapterInterface
    {
        $connectionOptions = $this->getConfiguredConnectionOptions($config);
        return new RedisAdapter(new Redis(), $connectionOptions);
    }

    /**
     * @param array|null $config
     * @return RedisClientAdapterInterface
     * @throws ConnectionException
     */
    protected function getRedisClientAdapter(?array $config = null): RedisClientAdapterInterface
    {
        return new RedisClientAdapter($this->getRedisAdapter($config));
    }

    protected function getConfiguredConnectionOptions(?array $config = null)
    {
        return new ConnectionOptions($config);
    }

    /**
     * @param RedisClientAdapterInterface $redisClientAdapter
     * @throws RedisBloomModuleNotInstalledException
     * @throws ResponseException
     */
    protected function validateRedisBloomModuleInstalled(RedisClientAdapterInterface $redisClientAdapter): void
    {
        $modules = $redisClientAdapter->executeRawCommand('MODULE', 'list');
        if (!$this->validator->isRedisBloomModuleInstalled($modules)) {
            throw new RedisBloomModuleNotInstalledException('RedisBloom module not installed in Redis server.');
        }
    }
}
