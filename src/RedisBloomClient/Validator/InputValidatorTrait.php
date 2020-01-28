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

namespace Averias\RedisBloom\Validator;

use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\ResponseException;

trait InputValidatorTrait
{
    /**
     * @param $value
     * @param string $valueName
     * @throws ResponseException
     */
    public function validateScalar($value, string $valueName): void
    {
        if (!is_string($value) && !is_numeric($value)) {
            throw new ResponseException(sprintf("%s value must be string or number", $valueName));
        }
    }

    /**
     * @param array $elements
     * @param string $elementsName
     * @throws ResponseException
     */
    public function validateArrayOfScalars(array $elements, string $elementsName): void
    {
        foreach ($elements as $element) {
            $this->validateScalar($element, $elementsName);
        }
    }

    /**
     * @param $value
     * @param string $valueName
     * @param float $min
     * @param float $max
     * @throws ResponseException
     */
    public function validateRange($value, string $valueName, float $min = 0.0, float $max = 1.0): void
    {
        if (!($min < $value && $value < $max)) {
            throw new ResponseException(
                sprintf("%s value must be > %.1f and < %.1f, provided value %f", $valueName, $min, $max, $value)
            );
        }
    }

    /**
     * @param $value
     * @param string $valueName
     * @param float $min
     * @param float $max
     * @throws ResponseException
     */
    public function validateRangeInclusiveMax($value, string $valueName, float $min = 0.0, float $max = 1.0): void
    {
        if (!($min < $value && $value <= $max)) {
            throw new ResponseException(
                sprintf("%s value must be > %.1f and <= %.1f, provided value %f", $valueName, $min, $max, $value)
            );
        }
    }

    public function validateFloat($value, string $valueName)
    {
        if (!is_float($value)) {
            throw new ResponseException(sprintf("%s value must be float", $valueName));
        }
    }

    /**
     * @param $value
     * @param string $valueName
     * @throws ResponseException
     */
    public function validateInteger($value, string $valueName)
    {
        if (!is_int($value)) {
            throw new ResponseException(sprintf("%s value must be integer", $valueName));
        }
    }

    /**
     * @param array $value
     * @param string $valueName
     * @throws ResponseException
     */
    public function validateEvenArrayDimension(array $value, string $valueName)
    {
        $length = count($value);
        if ($length % 2 != 0) {
            throw new ResponseException(
                sprintf("%s value must be an array with even length, length found: %d", $valueName, $length)
            );
        }
    }

    /**
     * @param array $itemsIncrease
     * @param string $commandName
     * @throws ResponseException
     */
    public function validateIncrementByItemsIncrease(array $itemsIncrease, string $commandName)
    {
        $this->validateEvenArrayDimension(
            $itemsIncrease,
            sprintf("item/increment params for %s", $commandName)
        );

        $itemName = '';
        foreach ($itemsIncrease as $index => $item) {
            if ($index % 2 == 0) {
                $this->validateScalar($item, sprintf("%s params", $commandName));
                $itemName = $item;
                continue;
            }
            $this->validateInteger($item, $itemName);
        }
    }

    public function validateNonScalingWithoutExpansion(array $options)
    {
        $optionsKeys = array_keys($options);
        if (in_array(OptionalParams::EXPANSION, $optionsKeys) && in_array(OptionalParams::NON_SCALING, $optionsKeys)) {
            throw new ResponseException("NONSCALING option is not allowed together with EXPANSION");
        }
    }
}
