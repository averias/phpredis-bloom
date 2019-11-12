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
    public function getRedisClientAdapter(array $config = []): RedisClientAdapterInterface
    {
        $redisClientAdapter = new RedisClientAdapter($this->getRedisAdapter($config));
        $this->validateRedisBloomModuleInstalled($redisClientAdapter);

        return $redisClientAdapter;
    }

    /**
     * @param array $config
     * @return RedisAdapterInterface
     * @throws ConnectionException
     */
    protected function getRedisAdapter(array $config = []): RedisAdapterInterface
    {
        $connectionOptions = new ConnectionOptions($config);
        return new RedisAdapter($connectionOptions);
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
