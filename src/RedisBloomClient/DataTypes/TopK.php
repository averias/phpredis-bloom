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

use Averias\RedisBloom\Command\TopK\TopKCommandTrait;

class TopK extends BaseDataType implements TopKInterface
{
    use TopKCommandTrait;

    /**
     * @inheritDoc
     */
    public function reserve(int $topK, int $width, int $depth, float $decay): bool
    {
        return $this->topKReserve($this->name, $topK, $width, $depth, $decay);
    }

    /**
     * @inheritDoc
     */
    public function add(...$items): array
    {
        return $this->topKAdd($this->name, ...$items);
    }

    /**
     * @inheritDoc
     */
    public function incrementBy(...$itemsIncrease): array
    {
        return $this->topKIncrementBy($this->name, ...$itemsIncrease);
    }

    /**
     * @inheritDoc
     */
    public function query(...$items): array
    {
        return $this->topKQuery($this->name, ...$items);
    }

    /**
     * @inheritDoc
     */
    public function count(...$items): array
    {
        return $this->topKCount($this->name, ...$items);
    }

    /**
     * @inheritDoc
     */
    public function list(): array
    {
        return $this->topKList($this->name);
    }

    /**
     * @inheritDoc
     */
    public function info(): array
    {
        return $this->topKInfo($this->name);
    }
}
