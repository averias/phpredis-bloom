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

namespace Averias\RedisBloom\Command\CountMinSketch;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Exception\ResponseException;

trait CountMinSketchCommandTrait
{
    /**
     * @param string $key
     * @param int $width
     * @param int $depth
     * @return bool
     */
    public function countMinSketchInitByDim(string $key, int $width, int $depth): bool
    {
        return $this->executeBloomCommand(BloomCommands::CMS_INITBYDIM, $key, [$width, $depth]);
    }

    /**
     * @param string $key
     * @param float $errorRate
     * @param float $probability
     * @return bool
     */
    public function countMinSketchInitByProb(string $key, float $errorRate, float $probability): bool
    {
        $this->validatePercentRate(
            $errorRate,
            sprintf("error param for %s command", BloomCommands::CMS_INITBYPROB)
        );
        $this->validatePercentRate(
            $probability,
            sprintf("probability param for %s command", BloomCommands::CMS_INITBYPROB)
        );

        return $this->executeBloomCommand(BloomCommands::CMS_INITBYPROB, $key, [$errorRate, $probability]);
    }

    /**
     * @param string $key
     * @param array $itemsIncrease
     * @return bool
     */
    public function countMinSketchIncrementBy(string $key, ...$itemsIncrease): bool
    {
        $this->validateEvenArrayDimension(
            $itemsIncrease,
            sprintf("item/increment params for %s", BloomCommands::CMS_INCRBY)
        );

        $itemName = '';
        foreach ($itemsIncrease as $index => $item) {
            if ($index % 2 == 0) {
                $this->validateScalar($item, sprintf("%s params", BloomCommands::CMS_INCRBY));
                $itemName = $item;
                continue;
            }
            $this->validateInteger($item, $itemName);
        }

        return $this->executeBloomCommand(BloomCommands::CMS_INCRBY, $key, $itemsIncrease);
    }

    /**
     * @param string $key
     * @param array $items
     * @return array
     */
    public function countMinSketchQuery(string $key, ...$items): array
    {
        $this->validateArrayOfScalars($items, sprintf("%s params", BloomCommands::CMS_QUERY));
        return $this->executeBloomCommand(BloomCommands::CMS_QUERY, $key, $items);
    }

    /**
     * @param string $destKey
     * @param int $numKeys
     * @param array $sketchKeys
     * @param array $weights
     * @return bool
     * @throws ResponseException
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
     * @param string $key
     * @return array
     */
    public function countMinSketchInfo(string $key): array
    {
        return $this->executeBloomCommand(BloomCommands::CMS_INFO, $key);
    }
}
