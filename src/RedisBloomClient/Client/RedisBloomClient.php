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

namespace Averias\RedisBloom\Client;

use Averias\RedisBloom\Command\BloomCommandTrait;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Validator\InputValidatorTrait;

class RedisBloomClient extends BaseRedisBloomClient implements RedisBloomClientInterface
{
    use BloomCommandTrait;
    use InputValidatorTrait;

    /**
     * @param string $commandName
     * @param array $arguments
     * @return mixed
     * @throws ResponseException
     */
    public function executeRawCommand(string $commandName, ...$arguments)
    {
        return $this->redisClientAdapter->executeRawCommand($commandName, ...$arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ResponseException
     */
    public function __call(string $name, array $arguments)
    {
        return $this->redisClientAdapter->executeCommandByName($name, $arguments);
    }
}
