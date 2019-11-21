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

namespace Averias\RedisBloom\Command\TopK;

use Averias\RedisBloom\Enum\BloomCommands;

trait TopKCommandTrait
{
    /**
     * @inheritDoc
     */
    public function topKReserve(string $key, int $topK, int $width, int $depth, float $decay): bool
    {
        $this->validateFloatRange(
            $decay,
            sprintf("decay param for %s command", BloomCommands::CMS_INITBYPROB),
            0.0,
            true,
            1.0,
            false
        );
        $arguments = [$topK, $width, $depth, $decay];
        return $this->executeBloomCommand(BloomCommands::TOPK_RESERVE, $key, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function topKAdd(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::TOPK_ADD));
        return $this->executeBloomCommand(BloomCommands::TOPK_ADD, $key, $items);
    }

    /**
     * @inheritDoc
     */
    public function topKIncrementBy(string $key, ...$itemsIncrease): array
    {
        $this->validateIncrementByItemsIncrease($itemsIncrease, BloomCommands::TOPK_INCRBY);
        return $this->executeBloomCommand(BloomCommands::TOPK_INCRBY, $key, $itemsIncrease);
    }

    /**
     * @inheritDoc
     */
    public function topKQuery(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::TOPK_QUERY));
        return $this->executeBloomCommand(BloomCommands::TOPK_QUERY, $key, $items);
    }

    /**
     * @inheritDoc
     */
    public function topKCount(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::TOPK_COUNT));
        return $this->executeBloomCommand(BloomCommands::TOPK_COUNT, $key, $items);
    }

    /**
     * @inheritDoc
     */
    public function topKList(string $key): array
    {
        return $this->executeBloomCommand(BloomCommands::TOPK_LIST, $key);
    }

    /**
     * @inheritDoc
     */
    public function topKInfo(string $key): array
    {
        return $this->executeBloomCommand(BloomCommands::TOPK_INFO, $key);
    }
}
