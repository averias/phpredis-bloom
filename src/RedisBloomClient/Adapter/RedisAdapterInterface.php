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

interface RedisAdapterInterface
{
    /**
     * @param ConnectionOptions $connectionOptions
     * @throws ConnectionException
     */
    public function __construct(ConnectionOptions $connectionOptions);

    /**
     * @return string|null
     */
    public function getLastError(): ?string;

    /**
     * @param string $commandName
     * @param mixed ...$arguments
     * @return mixed
     * @throws ConnectionException
     */
    public function rawCommand(string $commandName, ...$arguments);

    /**
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     * @throws ConnectionException
     */
    public function executeCommandByName(string $methodName, array $arguments = []);

    /**
     * @throws ConnectionException
     */
    public function checkConnection(): void;

    /**
     * @throws ConnectionException
     */
    public function setConnection(): void;

    /**
     * @return bool
     */
    public function connect(): bool;

    /**
     * @return bool
     */
    public function closeConnection(): bool;
}
