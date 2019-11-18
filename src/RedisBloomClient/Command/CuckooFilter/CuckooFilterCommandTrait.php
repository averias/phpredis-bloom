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

namespace Averias\RedisBloom\Command\CuckooFilter;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Enum\OptionalParams;

trait CuckooFilterCommandTrait
{
    /**
     * @inheritDoc
     */
    public function cuckooFilterReserve(string $key, int $capacity, array $options = []): bool
    {
        $parsedOptions = $this->parseRequest(BloomCommands::CF_RESERVE, $options);
        $arguments = array_merge([$capacity], $parsedOptions);

        return $this->executeBloomCommand(BloomCommands::CF_RESERVE, $key, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterAdd(string $key, $item): bool
    {
        $this->validateScalar($item, sprintf("%s params", BloomCommands::CF_ADD));
        return $this->executeBloomCommand(BloomCommands::CF_ADD, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterAddIfNotExist(string $key, $item): bool
    {
        $this->validateScalar($item, sprintf("%s params", BloomCommands::CF_ADDNX));
        return $this->executeBloomCommand(BloomCommands::CF_ADDNX, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterInsert(string $key, array $items, array $options = []): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::CF_INSERT));
        $parsedOptions = $this->parseRequest(BloomCommands::CF_INSERT, $options);
        $items = array_merge([OptionalParams::ITEMS], $items);
        $arguments = array_merge($parsedOptions, $items);

        return $this->executeBloomCommand(BloomCommands::CF_INSERT, $key, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterInsertIfNotExist(string $key, array $items, array $options = []): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::CF_INSERTNX));
        $parsedOptions = $this->parseRequest(BloomCommands::CF_INSERTNX, $options);
        $items = array_merge([OptionalParams::ITEMS], $items);
        $arguments = array_merge($parsedOptions, $items);

        return $this->executeBloomCommand(BloomCommands::CF_INSERTNX, $key, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterExists(string $key, $item): bool
    {
        $this->validateScalar($item, sprintf("%s params", BloomCommands::CF_EXISTS));
        return $this->executeBloomCommand(BloomCommands::CF_EXISTS, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterDelete(string $key, $item): bool
    {
        $this->validateScalar($item, sprintf("%s params", BloomCommands::CF_DEL));
        return $this->executeBloomCommand(BloomCommands::CF_DEL, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterCount(string $key, $item): int
    {
        $this->validateScalar($item, sprintf("%s params", BloomCommands::CF_COUNT));
        return $this->executeBloomCommand(BloomCommands::CF_COUNT, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterScanDump(string $key, int $iterator): array
    {
        return $this->executeBloomCommand(BloomCommands::CF_SCANDUMP, $key, [$iterator]);
    }

    /**
     * @inheritDoc
     */
    public function cuckooFilterLoadChunk(string $key, int $iterator, $data): bool
    {
        return $this->executeBloomCommand(BloomCommands::CF_LOADCHUNK, $key, [$iterator, $data]);
    }
}
