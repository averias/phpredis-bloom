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

namespace Averias\RedisBloom\Command\BloomFilter;

interface BloomFilterCommandTraitInterface
{
    /**
     * @param string $key
     * @param float $errorRate
     * @param int $capacity
     * @return bool
     */
    public function bloomFilterReserve(string $key, float $errorRate, int $capacity): bool;

    /**
     * @param string $key
     * @param $item
     * @return bool
     */
    public function bloomFilterAdd(string $key, $item): bool;

    /**
     * @param string $key
     * @param mixed ...$items
     * @return array
     */
    public function bloomFilterMultiAdd(string $key, ...$items): array;

    /**
     * @param string $key
     * @param array $items
     * @param array $options
     * @return array
     */
    public function bloomFilterInsert(string $key, array $items, array $options = []): array;

    /**
     * @param string $key
     * @param $item
     * @return bool
     */
    public function bloomFilterExists(string $key, $item): bool;

    /**
     * @param string $key
     * @param mixed ...$items
     * @return array
     */
    public function bloomFilterMultiExists(string $key, ...$items): array;

    /**
     * @param string $key
     * @param int $iterator
     * @return array
     */
    public function bloomFilterScanDump(string $key, int $iterator): array;

    /**
     * @param string $key
     * @param int $iterator
     * @param $data
     * @return bool
     */
    public function bloomFilterLoadChunk(string $key, int $iterator, $data): bool;
}
