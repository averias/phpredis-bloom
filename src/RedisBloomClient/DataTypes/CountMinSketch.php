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

use Averias\RedisBloom\Command\CountMinSketch\CountMinSketchCommandTrait;
use Averias\RedisBloom\Exception\ResponseException;

class CountMinSketch extends BaseDataType implements CountMinSketchInterface
{
    use CountMinSketchCommandTrait;

    /**
     * @param int $width
     * @param int $depth
     * @return bool
     */
    public function initByDim(int $width, int $depth): bool
    {
        return $this->countMinSketchInitByDim($this->name, $width, $depth);
    }

    /**
     * @param float $errorRate
     * @param float $probability
     * @return bool
     */
    public function initByProb(float $errorRate, float $probability): bool
    {
        return $this->countMinSketchInitByProb($this->name, $errorRate, $probability);
    }

    /**
     * @param array $itemsIncrease
     * @return bool
     */
    public function incrementBy(...$itemsIncrease): bool
    {
        return $this->countMinSketchIncrementBy($this->name, ...$itemsIncrease);
    }

    /**
     * @param array $items
     * @return array
     */
    public function query(...$items): array
    {
        return $this->countMinSketchQuery($this->name, ...$items);
    }

    /**
     * @param int $numKeys
     * @param array $sketchKeys
     * @param array $weights
     * @return bool
     * @throws ResponseException
     */
    public function mergeFrom(int $numKeys, array $sketchKeys, array $weights = []): bool
    {
        return $this->countMinSketchMerge($this->name, $numKeys, $sketchKeys, $weights);
    }

    /**
     * @return array
     */
    public function info(): array
    {
        return $this->countMinSketchInfo($this->name);
    }
}
