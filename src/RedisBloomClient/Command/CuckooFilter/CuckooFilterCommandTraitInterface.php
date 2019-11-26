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

namespace Averias\RedisBloom\Command\CuckooFilter;

interface CuckooFilterCommandTraitInterface
{
    /**
     * @param string $key
     * @param int $capacity
     * @param array $options
     * @return bool
     */
    public function cuckooFilterReserve(string $key, int $capacity, array $options = []): bool;

    /**
     * @param string $key
     * @param $item
     * @return bool
     */
    public function cuckooFilterAdd(string $key, $item): bool;

    /**
     * @param string $key
     * @param $item
     * @return bool
     */
    public function cuckooFilterAddIfNotExist(string $key, $item): bool;

    /**
     * @param string $key
     * @param array $items
     * @param array $options
     * @return array
     */
    public function cuckooFilterInsert(string $key, array $items, array $options = []): array;

    /**
     * @param string $key
     * @param array $items
     * @param array $options
     * @return array
     */
    public function cuckooFilterInsertIfNotExist(string $key, array $items, array $options = []): array;

    /**
     * @param string $key
     * @param $item
     * @return bool
     */
    public function cuckooFilterExists(string $key, $item): bool;

    /**
     * @param string $key
     * @param $item
     * @return bool
     */
    public function cuckooFilterDelete(string $key, $item): bool;

    /**
     * @param string $key
     * @param $item
     * @return int
     */
    public function cuckooFilterCount(string $key, $item): int;

    /**
     * @param string $key
     * @param int $iterator
     * @return array
     */
    public function cuckooFilterScanDump(string $key, int $iterator): array;

    /**
     * @param string $key
     * @param int $iterator
     * @param $data
     * @return bool
     */
    public function cuckooFilterLoadChunk(string $key, int $iterator, $data): bool;

    /**
     * @param string $key
     * @return array
     */
    public function cuckooFilterInfo(string $key): array;
}
