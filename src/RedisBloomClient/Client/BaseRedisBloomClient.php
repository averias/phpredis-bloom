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

namespace Averias\RedisBloom\Client;

use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\ParserTrait;

class BaseRedisBloomClient
{
    use ParserTrait;

    /** @var RedisClientAdapterInterface */
    protected $redisClientAdapter;

    public function __construct(RedisClientAdapterInterface $redisClientAdapter)
    {
        $this->redisClientAdapter = $redisClientAdapter;
    }

    /**
     * @return bool
     */
    public function disconnect()
    {
        return $this->redisClientAdapter->disconnect();
    }

    /**
     * @param string $command
     * @param string $key
     * @param array $params
     * @return mixed
     * @throws ResponseException
     */
    protected function executeBloomCommand(string $command, string $key, array $params = [])
    {
        $response = $this->redisClientAdapter->executeBloomCommand($command, $key, $params);
        return $this->parseResponse($command, $response);
    }
}
