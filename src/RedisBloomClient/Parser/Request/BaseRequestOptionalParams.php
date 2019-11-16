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

namespace Averias\RedisBloom\Parser\Request;

use Averias\RedisBloom\Enum\OptionalParams;
use InvalidArgumentException;

class BaseRequestOptionalParams
{
    /**
     * @param array $defaultOptionalParams
     * @param array $requestedOptionalParams
     * @return array
     */
    protected function getMergedOptionalParams(array $defaultOptionalParams, array $requestedOptionalParams): array
    {
        return array_merge($defaultOptionalParams, $requestedOptionalParams);
    }

    /**
     * @param array $result
     * @param array $options
     * @return array
     */
    protected function appendCapacity(array $result, array $options): array
    {
        $capacity = $options[OptionalParams::CAPACITY];
        if (!is_null($capacity)) {
            if (!is_int($capacity)) {
                throw new InvalidArgumentException(sprintf("option %s must be integer", OptionalParams::CAPACITY));
            }
            $result[] = OptionalParams::CAPACITY;
            $result[] = $capacity;
        }

        return $result;
    }

    /**
     * @param array $result
     * @param array $options
     * @return array
     */
    protected function appendErrorRate(array $result, array $options): array
    {
        $error = $options[OptionalParams::ERROR];

        if (!is_null($error)) {
            if (!is_float($error)) {
                throw new InvalidArgumentException(sprintf("option %s must be float", OptionalParams::ERROR));
            }
            if ($error <= 0.0 || $error > 1.0) {
                throw new InvalidArgumentException(
                    sprintf("option %s must be >= 0.0 and >= 1.0, provided value %f", OptionalParams::ERROR, $error)
                );
            }
            $result[] = OptionalParams::ERROR;
            $result[] = $error;
        }

        return $result;
    }

    /**
     * @param array $result
     * @param array $options
     * @return array
     */
    protected function appendNoCreate(array $result, array $options): array
    {
        $noCreate = $options[OptionalParams::NO_CREATE];
        if (!is_null($noCreate) && true === $noCreate) {
            $result[] = OptionalParams::NO_CREATE;
        }

        return $result;
    }
}
