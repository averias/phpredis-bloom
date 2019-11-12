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
use Redis;

class RedisAdapter implements RedisAdapterInterface
{
    /** @var ConnectionOptions */
    protected $connectionOptions;

    /** @var Redis */
    protected $redis;

    /**
     * @param ConnectionOptions $connectionOptions
     * @throws ConnectionException
     */
    public function __construct(ConnectionOptions $connectionOptions)
    {
        $this->connectionOptions = $connectionOptions;
        $this->redis = new Redis();
        $this->setConnection();
    }

    public function getLastError(): ?string
    {
        return $this->redis->getLastError();
    }

    /**
     * @param string $commandName
     * @param mixed ...$arguments
     * @return mixed
     * @throws ConnectionException
     */
    public function rawCommand(string $commandName, ...$arguments)
    {
        $this->checkConnection();
        return $this->redis->rawCommand($commandName, ...$arguments);
    }

    /**
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws ConnectionException
     */
    public function executeCommandByName(string $methodName, array $arguments = [])
    {
        $this->checkConnection();
        return call_user_func_array([$this->redis, $methodName], $arguments);
    }

    /**
     * @throws ConnectionException
     */
    public function checkConnection(): void
    {
        if (!$this->redis->isConnected()) {
            $this->setConnection();
        }
    }

    /**
     * @throws ConnectionException
     */
    public function setConnection(): void
    {
        if (!$this->connect()) {
            throw new ConnectionException(
                sprintf("connection to Redis server failed, reason: %s", $this->redis->getLastError())
            );
        }

        if ($this->connectionOptions->getDatabase() != 0) {
            $this->redis->select($this->connectionOptions->getDatabase());
        }

        $this->redis->setOption(Redis::OPT_REPLY_LITERAL, 1);
    }

    /**
     * @return bool
     */
    public function connect(): bool
    {
        $connectionValues = $this->connectionOptions->getConnectionValues();
        if ($this->connectionOptions->isPersistent()) {
            return  $this->redis->pconnect(...$connectionValues);
        }

        return $this->redis->connect(...$connectionValues);
    }

    /**
     * @return bool
     */
    public function closeConnection(): bool
    {
        return $this->redis->close();
    }
}
