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

interface BloomFilterInterface extends DataTypeInterface
{
    /**
     * @param float $errorRate
     * @param int $capacity
     * @return bool
     */
    public function reserve(float $errorRate, int $capacity): bool;

    /**
     * @param mixed $item
     * @return bool
     */
    public function add($item): bool;

    /**
     * @param mixed ...$items
     * @return array
     */
    public function multiAdd(...$items): array;

    /**
     * @param array $items
     * @param array $options
     * @return array
     */
    public function insert(array $items, array $options = []): array;

    /**
     * @param mixed $item
     * @return bool
     */
    public function exists($item): bool;

    /**
     * @param mixed ...$items
     * @return array
     */
    public function multiExists(...$items): array;

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
}
