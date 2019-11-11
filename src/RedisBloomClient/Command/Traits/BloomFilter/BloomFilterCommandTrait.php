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

namespace Averias\RedisBloom\Command\Traits\BloomFilter;

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
        foreach ($items as $item) {
            $this->validateScalar($item);
        }

        return $this->executeBloomCommand(BloomCommands::BF_MADD, $key, $items);
    }

    public function bloomFilterInsert(string $key, array $items, array $options = []): array
    {
        foreach ($items as $item) {
            $this->validateScalar($item);
        }

        $parserFactory = $this->getRequestParserFactory();
        $requestParser = $parserFactory->getBloomFilterInsertOptionalParams();
        $parsedOptions = $requestParser->parse($options);

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
        foreach ($items as $item) {
            $this->validateScalar($item);
        }

        return $this->executeBloomCommand(BloomCommands::BF_MEXISTS, $key, $items);
    }

    public function bloomFilterScanDump(string $key, $iterator)
    {

    }

    public function bloomFilterLoadChunk(string $key, $iterator, $data)
    {

    }
}
