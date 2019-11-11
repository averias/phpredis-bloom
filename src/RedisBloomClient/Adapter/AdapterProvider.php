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
    public function get(array $config = []): RedisClientAdapterInterface
    {
        $redisClient = $this->getRedisClient($config);

        $modules = $redisClient->executeRawCommand('MODULE', 'list');
        if (!$this->validator->isRedisBloomModuleInstalled($modules)) {
            throw new RedisBloomModuleNotInstalledException('RedisBloom module not installed in Redis server.');
        }

        return $redisClient;
    }

    /**
     * @param array $config
     * @return RedisClientAdapter
     * @throws ConnectionException
     */
    protected function getRedisClient(array $config = []): RedisClientAdapter
    {
        $connectionOptions = new ConnectionOptions($config);
        return new RedisClientAdapter(new Redis(), $connectionOptions);
    }
}