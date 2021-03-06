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

use Averias\RedisBloom\Command\BloomFilter\BloomFilterCommandTrait;
use Exception;

class BloomFilter extends BaseDataType implements BloomFilterInterface
{
    use BloomFilterCommandTrait;

    /**
     * @inheritDoc
     */
    public function reserve(float $errorRate, int $capacity, array $options = []): bool
    {
        return $this->bloomFilterReserve($this->name, $errorRate, $capacity, $options);
    }

    /**
     * @inheritDoc
     */
    public function add($item): bool
    {
        return $this->bloomFilterAdd($this->name, $item);
    }

    /**
     * @inheritDoc
     */
    public function multiAdd(...$items): array
    {
        return $this->bloomFilterMultiAdd($this->name, ...$items);
    }

    /**
     * @inheritDoc
     */
    public function insert(array $items, array $options = []): array
    {
        return $this->bloomFilterInsert($this->name, $items, $options);
    }

    /**
     * @inheritDoc
     */
    public function exists($item): bool
    {
        return $this->bloomFilterExists($this->name, $item);
    }

    /**
     * @inheritDoc
     */
    public function multiExists(...$items): array
    {
        return $this->bloomFilterMultiExists($this->name, ...$items);
    }

    /**
     * @inheritDoc
     */
    public function scanDump(int $iterator): array
    {
        return $this->bloomFilterScanDump($this->name, $iterator);
    }

    /**
     * @inheritDoc
     */
    public function loadChunk(int $iterator, $data): bool
    {
        return $this->bloomFilterLoadChunk($this->name, $iterator, $data);
    }

    /**
     * @inheritDoc
     */
    public function copy(string $targetFilter): bool
    {
        try {
            $iterator = 0;
            while (true) {
                list ($iterator, $data) = $this->scanDump($iterator);
                if ($iterator == 0) {
                    break;
                }
                $this->bloomFilterLoadChunk($targetFilter, $iterator, $data);
            }
        } catch (Exception $e) {
            $this->copyFailedException($targetFilter, $e->getMessage());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function info(): array
    {
        return $this->bloomFilterInfo($this->name);
    }
}
