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

class CountMinSketch extends BaseDataType implements CountMinSketchInterface
{
    use CountMinSketchCommandTrait;

    /**
     * @inheritDoc
     */
    public function initByDim(int $width, int $depth): bool
    {
        return $this->countMinSketchInitByDim($this->name, $width, $depth);
    }

    /**
     * @inheritDoc
     */
    public function initByProb(float $errorRate, float $probability): bool
    {
        return $this->countMinSketchInitByProb($this->name, $errorRate, $probability);
    }

    /**
     * @inheritDoc
     */
    public function incrementBy(...$itemsIncrease): array
    {
        return $this->countMinSketchIncrementBy($this->name, ...$itemsIncrease);
    }

    /**
     * @inheritDoc
     */
    public function query(...$items): array
    {
        return $this->countMinSketchQuery($this->name, ...$items);
    }

    /**
     * @inheritDoc
     */
    public function mergeFrom(int $numKeys, array $sketchKeys, array $weights = []): bool
    {
        return $this->countMinSketchMerge($this->name, $numKeys, $sketchKeys, $weights);
    }

    /**
     * @inheritDoc
     */
    public function info(): array
    {
        return $this->countMinSketchInfo($this->name);
    }
}
