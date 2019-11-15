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
    public function bloomFilterReserve(string $key, float $errorRate, int $capacity): bool;

    public function bloomFilterAdd(string $key, $item): bool;

    public function bloomFilterMultiAdd(string $key, ...$items): array;

    public function bloomFilterInsert(string $key, array $items, array $options = []): array;

    public function bloomFilterExists(string $key, $item): bool;

    public function bloomFilterMultiExists(string $key, ...$items): array;

    public function bloomFilterScanDump(string $key, int $iterator): array;

    public function bloomFilterLoadChunk(string $key, int $iterator, $data): bool;
}
