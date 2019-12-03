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

namespace Averias\RedisBloom\DataTypes;

use Averias\RedisBloom\Exception\ResponseException;

interface CountMinSketchInterface extends DataTypeInterface
{
    /**
     * @param int $width
     * @param int $depth
     * @return bool
     * @throws ResponseException
     */
    public function initByDim(int $width, int $depth): bool;

    /**
     * @param float $errorRate
     * @param float $probability
     * @return bool
     * @throws ResponseException
     */
    public function initByProb(float $errorRate, float $probability): bool;

    /**
     * @param array $itemsIncrease
     * @return array
     * @throws ResponseException
     */
    public function incrementBy(...$itemsIncrease): array;

    /**
     * @param array $items
     * @return array
     * @throws ResponseException
     */
    public function query(...$items): array;

    /**
     * @param int $numKeys
     * @param array $sketchKeys
     * @param array $weights
     * @return bool
     * @throws ResponseException
     */
    public function mergeFrom(int $numKeys, array $sketchKeys, array $weights = []): bool;

    /**
     * @return array
     * @throws ResponseException
     */
    public function info(): array;
}
