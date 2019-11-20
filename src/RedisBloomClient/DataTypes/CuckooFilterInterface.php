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

namespace Averias\RedisBloom\DataTypes;

use Averias\RedisBloom\Exception\ResponseException;

interface CuckooFilterInterface extends DataTypeInterface
{
    /**
     * @param int $capacity
     * @param array $options
     * @return bool
     */
    public function reserve(int $capacity, array $options = []): bool;

    /**
     * @param mixed $item
     * @return bool
     */
    public function add($item): bool;

    /**
     * @param mixed $item
     * @return bool
     */
    public function addIfNotExist($item): bool;

    /**
     * @param array $items
     * @param array $options
     * @return array
     */
    public function insert(array $items, array $options = []): array;

    /**
     * @param array $items
     * @param array $options
     * @return array
     */
    public function insertIfNotExist(array $items, array $options = []): array;

    /**
     * @param mixed $item
     * @return bool
     */
    public function exists($item): bool;

    /**
     * @param mixed $item
     * @return bool
     */
    public function delete($item): bool;

    /**
     * @param mixed $item
     * @return int
     */
    public function count($item): int;

    /**
     * @param int $iterator
     * @return array
     */
    public function scanDump(int $iterator): array;

    /**
     * @param int $iterator
     * @param mixed $data
     * @return bool
     */
    public function loadChunk(int $iterator, $data): bool;

    /**
     * @param string $targetFilter
     * @return bool
     * @throws ResponseException
     */
    public function copy(string $targetFilter): bool;

    /**
     * @return array
     */
    public function info(): array;
}
