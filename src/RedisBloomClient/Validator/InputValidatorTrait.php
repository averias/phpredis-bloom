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
     * @throws ResponseException
     */
    public function validatePercentRate($value, string $valueName)
    {
        if (!is_float($value)) {
            throw new ResponseException(sprintf("%s value must be float", $valueName));
        }
        if ($value <= 0.0 || $value > 1.0) {
            throw new ResponseException(
                sprintf("%s value must be > 0.0 and <= 1.0, provided value %f", $valueName, $value)
            );
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
}
