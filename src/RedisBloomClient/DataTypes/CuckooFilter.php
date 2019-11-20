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

use Averias\RedisBloom\Command\CuckooFilter\CuckooFilterCommandTrait;
use Averias\RedisBloom\Exception\ResponseException;
use Exception;

class CuckooFilter extends BaseDataType implements CuckooFilterInterface
{
    use CuckooFilterCommandTrait;

    /**
     * @param int $capacity
     * @param array $options
     * @return bool
     */
    public function reserve(int $capacity, array $options = []): bool
    {
        return $this->cuckooFilterReserve($this->name, $capacity, $options);
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function add($item): bool
    {
        return $this->cuckooFilterAdd($this->name, $item);
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function addIfNotExist($item): bool
    {
        return $this->cuckooFilterAddIfNotExist($this->name, $item);
    }

    /**
     * @param array $items
     * @param array $options
     * @return array
     */
    public function insert(array $items, array $options = []): array
    {
        return $this->cuckooFilterInsert($this->name, $items, $options);
    }

    /**
     * @param array $items
     * @param array $options
     * @return array
     */
    public function insertIfNotExist(array $items, array $options = []): array
    {
        return $this->cuckooFilterInsertIfNotExist($this->name, $items, $options);
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function exists($item): bool
    {
        return $this->cuckooFilterExists($this->name, $item);
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public function delete($item): bool
    {
        return $this->cuckooFilterDelete($this->name, $item);
    }

    /**
     * @param mixed $item
     * @return int
     */
    public function count($item): int
    {
        return $this->cuckooFilterCount($this->name, $item);
    }

    /**
     * @param int $iterator
     * @return array
     */
    public function scanDump(int $iterator): array
    {
        return $this->cuckooFilterScanDump($this->name, $iterator);
    }

    /**
     * @param int $iterator
     * @param mixed $data
     * @return bool
     */
    public function loadChunk(int $iterator, $data): bool
    {
        return $this->cuckooFilterLoadChunk($this->name, $iterator, $data);
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
                $this->cuckooFilterLoadChunk($targetFilter, $iterator, $data);
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

    /**
     * @inheritDoc
     */
    public function info(): array
    {
        return $this->cuckooFilterInfo($this->name);
    }
}
