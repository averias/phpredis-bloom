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
    /**
     * @inheritDoc
     */
    public function bloomFilterReserve(string $key, float $errorRate, int $capacity, array $options = []): bool
    {
        $parsedOptions = $this->parseRequest(BloomCommands::BF_RESERVE, $options);
        $arguments = array_merge([$errorRate, $capacity], $parsedOptions);
        return $this->executeBloomCommand(BloomCommands::BF_RESERVE, $key, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterAdd(string $key, $item): bool
    {
        $this->validateScalar($item, sprintf("%s param", BloomCommands::BF_ADD));
        return $this->executeBloomCommand(BloomCommands::BF_ADD, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterMultiAdd(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::BF_MADD));
        return $this->executeBloomCommand(BloomCommands::BF_MADD, $key, $items);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterInsert(string $key, array $items, array $options = []): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::BF_INSERT));
        $parsedOptions = $this->parseRequest(BloomCommands::BF_INSERT, $options);
        $items = array_merge([OptionalParams::ITEMS], $items);
        $arguments = array_merge($parsedOptions, $items);

        return $this->executeBloomCommand(BloomCommands::BF_INSERT, $key, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterExists(string $key, $item): bool
    {
        $this->validateScalar($item, sprintf("%s params", BloomCommands::BF_EXISTS));
        return $this->executeBloomCommand(BloomCommands::BF_EXISTS, $key, [$item]);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterMultiExists(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::BF_MEXISTS));
        return $this->executeBloomCommand(BloomCommands::BF_MEXISTS, $key, $items);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterScanDump(string $key, int $iterator): array
    {
        return $this->executeBloomCommand(BloomCommands::BF_SCANDUMP, $key, [$iterator]);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterLoadChunk(string $key, int $iterator, $data): bool
    {
        return $this->executeBloomCommand(BloomCommands::BF_LOADCHUNK, $key, [$iterator, $data]);
    }

    /**
     * @inheritDoc
     */
    public function bloomFilterInfo(string $key): array
    {
        return $this->executeBloomCommand(BloomCommands::BF_INFO, $key);
    }

    abstract protected function executeBloomCommand(string $command, string $key, array $params = []);

    abstract public function validateScalar($value, string $valueName);

    abstract public function validateArrayOfScalars(array $elements, string $elementsName);

    abstract public function parseRequest(string $command, $input);
}
