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

use Averias\RedisBloom\Command\Traits\BloomFilter\BloomFilterCommandTrait;
use Averias\RedisBloom\Exception\ResponseException;
use Exception;

class BloomFilter extends BaseDataType
{
    use BloomFilterCommandTrait;

    public function reserve(float $errorRate, int $capacity): bool
    {
        return $this->bloomFilterReserve($this->name, $errorRate, $capacity);
    }

    public function add($item): bool
    {
        return $this->bloomFilterAdd($this->name, $item);
    }

    public function multiAdd(...$items): array
    {
        return $this->bloomFilterMultiAdd($this->name, ...$items);
    }

    public function insert(array $items, array $options = []): array
    {
        return $this->bloomFilterInsert($this->name, $items, $options);
    }

    public function exists($item): bool
    {
        return $this->bloomFilterExists($this->name, $item);
    }

    public function multiExists(...$items): array
    {
        return $this->bloomFilterMultiExists($this->name, ...$items);
    }

    public function scanDump(int $iterator): array
    {
        return $this->bloomFilterScanDump($this->name, $iterator);
    }

    public function loadChunk(int $iterator, $data, string $targetFilter = null): bool
    {
        $filterKey = $targetFilter ?? $this->name;
        return $this->bloomFilterLoadChunk($filterKey, $iterator, $data);
    }

    /**
     * @param string $targetFilter
     * @return bool
     * @throws ResponseException
     */
    public function copy(string $targetFilter): bool
    {
        $message = '';

        try {
            $iterator = 0;
            while (true) {
                list ($iterator, $data) = $this->scanDump($iterator);
                if ($iterator == 0) {
                    break;
                }
                $this->loadChunk($iterator, $data, $targetFilter);
            }
            $success = true;
        } catch (Exception $e) {
            $success = false;
            $message = sprintf(
                "copying data to '%s' target filter failed, reason %s",
                $targetFilter,
                $e->getMessage()
            );
        }

        if (!$success) {
            try {
                $this->redisClientAdapter->executeCommandByName('del', [$targetFilter]);
            } catch (Exception $exception) {
                throw new ResponseException(
                    sprintf(
                        "%s, '%s' target filter could NOT be deleted, please delete it manually.",
                        $message,
                        $targetFilter
                    )
                );
            }
            throw new ResponseException(sprintf("%s, '%s' target filter was deleted.", $message, $targetFilter));
        }

        return $success;
    }
}
