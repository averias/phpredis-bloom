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

use Averias\RedisBloom\Exception\ConnectionException;

interface RedisAdapterInterface
{
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

    /**
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * @return string
     */
    public function getConnectionHost(): string;

    /**
     * @return int
     */
    public function getConnectionPort(): int;

    /**
     * @return int
     */
    public function getConnectionDatabase(): int;
}
