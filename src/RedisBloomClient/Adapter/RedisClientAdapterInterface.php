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

use Averias\RedisBloom\Exception\ResponseException;

interface RedisClientAdapterInterface
{
    /**
     * @param string $command
     * @param string $key
     * @param array $params
     * @return mixed
     * @throws ResponseException
     */
    public function executeBloomCommand(string $command, string $key, array $params = []);

    /**
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws ResponseException
     */
    public function executeCommandByName(string $methodName, array $arguments);

    /**
     * @param string $commandName
     * @param array $arguments
     * @return mixed
     * @throws ResponseException
     */
    public function executeRawCommand(string $commandName, ...$arguments);
}