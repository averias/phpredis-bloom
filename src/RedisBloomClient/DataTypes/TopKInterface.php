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

interface TopKInterface extends DataTypeInterface
{
    /**
     * @param int $topK
     * @param int $width
     * @param int $depth
     * @param float $decay
     * @return bool
     */
    public function reserve(int $topK, int $width, int $depth, float $decay): bool;

    /**
     * @param mixed ...$items
     * @return array
     */
    public function add(...$items): array;

    /**
     * @param mixed ...$itemsIncrease
     * @return array
     */
    public function incrementBy(...$itemsIncrease): array;

    /**
     * @param mixed ...$items
     * @return array
     */
    public function query(...$items): array;

    /**
     * @param mixed ...$items
     * @return array
     */
    public function count(...$items): array;

    /**
     * @return array
     */
    public function list(): array;

    /**
     * @return array
     */
    public function info(): array;
}
