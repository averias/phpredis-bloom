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

namespace Averias\RedisBloom\Command\TopK;

interface TopKCommandTraitInterface
{
    /**
     * @param string $key
     * @param int $topK
     * @param int $width
     * @param int $depth
     * @param float $decay
     * @return bool
     */
    public function topKReserve(string $key, int $topK, int $width, int $depth, float $decay): bool;

    /**
     * @param string $key
     * @param mixed ...$items
     * @return array
     */
    public function topKAdd(string $key, ...$items): array;

    /**
     * @param string $key
     * @param mixed ...$itemsIncrease
     * @return array
     */
    public function topKIncrementBy(string $key, ...$itemsIncrease): array;

    /**
     * @param string $key
     * @param mixed ...$items
     * @return array
     */
    public function topKQuery(string $key, ...$items): array;

    /**
     * @param string $key
     * @param mixed ...$items
     * @return array
     */
    public function topKCount(string $key, ...$items): array;

    /**
     * @param string $key
     * @return array
     */
    public function topKList(string $key): array;

    /**
     * @param string $key
     * @return array
     */
    public function topKInfo(string $key): array;
}
