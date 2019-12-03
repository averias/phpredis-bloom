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

namespace Averias\RedisBloom\Command\CountMinSketch;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Exception\ResponseException;

trait CountMinSketchCommandTrait
{
    /**
     * @inheritDoc
     */
    public function countMinSketchInitByDim(string $key, int $width, int $depth): bool
    {
        return $this->executeBloomCommand(BloomCommands::CMS_INITBYDIM, $key, [$width, $depth]);
    }

    /**
     * @inheritDoc
     */
    public function countMinSketchInitByProb(string $key, float $errorRate, float $probability): bool
    {
        $this->validateFloatRange(
            $errorRate,
            sprintf("error param for %s command", BloomCommands::CMS_INITBYPROB)
        );
        $this->validateFloatRange(
            $probability,
            sprintf("probability param for %s command", BloomCommands::CMS_INITBYPROB)
        );

        return $this->executeBloomCommand(BloomCommands::CMS_INITBYPROB, $key, [$errorRate, $probability]);
    }

    /**
     * @inheritDoc
     */
    public function countMinSketchIncrementBy(string $key, ...$itemsIncrease): array
    {
        $this->validateIncrementByItemsIncrease($itemsIncrease, BloomCommands::CMS_INCRBY);
        return $this->executeBloomCommand(BloomCommands::CMS_INCRBY, $key, $itemsIncrease);
    }

    /**
     * @inheritDoc
     */
    public function countMinSketchQuery(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::CMS_QUERY));
        return $this->executeBloomCommand(BloomCommands::CMS_QUERY, $key, $items);
    }

    /**
     * @inheritDoc
     */
    public function countMinSketchMerge(string $destKey, int $numKeys, array $sketchKeys, array $weights = []): bool
    {
        $sketchKeysLength = count($sketchKeys);
        if ($numKeys != $sketchKeysLength) {
            throw new ResponseException(
                sprintf(
                    "numKeys param value (%d) does not mismatch with the length of sketchKeys param value (%d)",
                    $numKeys,
                    $sketchKeysLength
                )
            );
        }

        $weightsLength = count($weights);
        if ($numKeys < $weightsLength) {
            throw new ResponseException(
                sprintf(
                    "num of WEIGHTS params (%d) must be <= numKeys param value (%d)",
                    $numKeys,
                    $weightsLength
                )
            );
        }

        $parsedWeights = $this->parseRequest(BloomCommands::CMS_MERGE, $weights);
        $arguments = array_merge([$numKeys], $sketchKeys, $parsedWeights);

        return $this->executeBloomCommand(BloomCommands::CMS_MERGE, $destKey, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function countMinSketchInfo(string $key): array
    {
        return $this->executeBloomCommand(BloomCommands::CMS_INFO, $key);
    }

    abstract protected function executeBloomCommand(string $command, string $key, array $params = []);

    abstract public function validateFloatRange(
        $value,
        string $valueName,
        $minValue = 0.0,
        $isExclusiveMin = true,
        $maxValue = 1.0,
        $isExclusiveMax = true
    );

    abstract public function validateArrayOfScalars(array $elements, string $elementsName);

    abstract public function parseRequest(string $command, $input);

    abstract public function validateIncrementByItemsIncrease(array $itemsIncrease, string $commandName);
}
