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
use Averias\RedisBloom\Enum\ResponseParser;
use Averias\RedisBloom\Exception\ConnectionException;
use Averias\RedisBloom\Exception\ResponseException;
use Redis;
use Exception;

class RedisClientAdapter implements RedisClientAdapterInterface
{
    /** @var Redis */
    protected $redis;

    /** @var ConnectionOptions */
    protected $connectionOptions;

    /**
     * @param Redis $redis
     * @param ConnectionOptions $connectionOptions
     * @throws ConnectionException
     */
    public function __construct(Redis $redis, ConnectionOptions $connectionOptions)
    {
        $this->redis = $redis;
        $this->connectionOptions = $connectionOptions;
        $this->setConnection();
    }

    /**
     * @param string $command
     * @param string $key
     * @param array $params
     * @return mixed
     * @throws ResponseException
     */
    public function executeBloomCommand(string $command, string $key, array $params = [])
    {
        $response = $this->executeRawCommand($command, $key, ...$params);

        if ($response === false) {
            $error = $this->redis->getLastError() ?? 'unknown';
            throw new ResponseException(
                sprintf("something was wrong when executing %s command, possible reasons: %s", $command, $error)
            );
        }

        return $this->parseResponse($command, $response);
    }

    /**
     * @param string $commandName
     * @param mixed ...$arguments
     * @return mixed
     * @throws ResponseException
     */
    public function executeRawCommand(string $commandName, ...$arguments)
    {
        try {
            $this->checkConnection();
            return $this->redis->rawCommand($commandName, ...$arguments);
        } catch (Exception $e) {
            throw new ResponseException(
                sprintf(
                    'the following error happened when executing the command "%s": %s',
                    $commandName,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws ResponseException
     */
    public function executeCommandByName(string $methodName, array $arguments = [])
    {
        try {
            $this->checkConnection();
            return call_user_func_array([$this->redis, $methodName], $arguments);
        } catch (Exception $e) {
            throw new ResponseException(
                sprintf(
                    'the following error happened when executing command "%s" with param "%s": %s',
                    $methodName,
                    implode(' ', $arguments),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @throws ConnectionException
     */
    protected function setConnection(): void
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
    protected function connect(): bool
    {
        $connectionValues = $this->connectionOptions->getConnectionValues();
        if ($this->connectionOptions->isPersistent()) {
            return  $this->redis->pconnect(...$connectionValues);
        }

        return $this->redis->connect(...$connectionValues);
    }

    /**
     * @throws ConnectionException
     */
    protected function checkConnection(): void
    {
        if (!$this->redis->isConnected()) {
            $this->setConnection();
        }
    }

    protected function parseResponse(string $command, $response)
    {
        $responseParsers = ResponseParser::RESPONSE_PARSER;
        if (isset($responseParsers[$command])) {
            $className = $responseParsers[$command];
            $parser = new $className();
            return $parser->parse($response);
        }

        return $response;
    }
}
