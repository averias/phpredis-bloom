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

namespace Averias\RedisBloom\Command\CountMinSketch;

interface CountMinSketchCommandTraitInterface
{
    /**
     * @param string $key
     * @param int $width
     * @param int $depth
     * @return bool
     */
    public function countMinSketchInitByDim(string $key, int $width, int $depth): bool;

    /**
     * @param string $key
     * @param float $errorRate
     * @param float $probability
     * @return bool
     */
    public function countMinSketchInitByProb(string $key, float $errorRate, float $probability): bool;

    /**
     * @param string $key
     * @param array $itemsIncrease
     * @return bool
     */
    public function countMinSketchIncrementBy(string $key, ...$itemsIncrease): bool;

    /**
     * @param string $key
     * @param array $items
     * @return array
     */
    public function countMinSketchQuery(string $key, ...$items): array;

    /**
     * @param string $destKey
     * @param int $numKeys
     * @param array $sketchKeys
     * @param array $weights
     * @return bool
     */
    public function countMinSketchMerge(string $destKey, int $numKeys, array $sketchKeys, array $weights = []): bool;

    /**
     * @param string $key
     * @return array
     */
    public function countMinSketchInfo(string $key): array;
}
