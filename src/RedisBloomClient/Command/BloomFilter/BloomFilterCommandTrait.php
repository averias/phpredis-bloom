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

namespace Averias\RedisBloom\Command\BloomFilter;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Enum\OptionalParams;

trait BloomFilterCommandTrait
{
    public function bloomFilterReserve(string $key, float $errorRate, int $capacity): bool
    {
        return $this->executeBloomCommand(BloomCommands::BF_RESERVE, $key, [$errorRate, $capacity]);
    }

    public function bloomFilterAdd(string $key, $item): bool
    {
        $this->validateScalar($item);
        return $this->executeBloomCommand(BloomCommands::BF_ADD, $key, [$item]);
    }

    public function bloomFilterMultiAdd(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items);
        return $this->executeBloomCommand(BloomCommands::BF_MADD, $key, $items);
    }

    public function bloomFilterInsert(string $key, array $items, array $options = []): array
    {
        $this->validateArrayOfScalars($items);
        $parsedOptions = $this->parseRequest(BloomCommands::BF_INSERT, $options);
        $items = array_merge([OptionalParams::ITEMS], $items);
        $arguments = array_merge($parsedOptions, $items);

        return $this->executeBloomCommand(BloomCommands::BF_INSERT, $key, $arguments);
    }

    public function bloomFilterExists(string $key, $item): bool
    {
        $this->validateScalar($item);
        return $this->executeBloomCommand(BloomCommands::BF_EXISTS, $key, [$item]);
    }

    public function bloomFilterMultiExists(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items);
        return $this->executeBloomCommand(BloomCommands::BF_MEXISTS, $key, $items);
    }

    public function bloomFilterScanDump(string $key, int $iterator): array
    {
        return $this->executeBloomCommand(BloomCommands::BF_SCANDUMP, $key, [$iterator]);
    }

    public function bloomFilterLoadChunk(string $key, int $iterator, $data): bool
    {
        return $this->executeBloomCommand(BloomCommands::BF_LOADCHUNK, $key, [$iterator, $data]);
    }
}
