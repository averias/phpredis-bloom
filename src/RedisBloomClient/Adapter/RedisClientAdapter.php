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

use Averias\RedisBloom\Exception\ResponseException;
use Exception;

class RedisClientAdapter implements RedisClientAdapterInterface
{
    /** @var RedisAdapterInterface */
    protected $redisAdapter;

    /**
     * @param RedisAdapterInterface $redisAdapter
     */
    public function __construct(RedisAdapterInterface $redisAdapter)
    {
        $this->redisAdapter = $redisAdapter;
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
            $error = $this->redisAdapter->getLastError() ?? 'unknown';
            throw new ResponseException(
                sprintf("something was wrong when executing %s command, possible reasons: %s", $command, $error)
            );
        }

        return $response;
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
            return $this->redisAdapter->rawCommand($commandName, ...$arguments);
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
            return $this->redisAdapter->executeCommandByName($methodName, $arguments);
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
     * @return bool
     */
    public function disconnect(): bool
    {
        return $this->redisAdapter->closeConnection();
    }
}
