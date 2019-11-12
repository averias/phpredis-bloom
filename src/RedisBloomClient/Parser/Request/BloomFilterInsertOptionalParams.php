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
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\ParserInterface;
use Throwable;
use InvalidArgumentException;

class BloomFilterInsertOptionalParams implements ParserInterface
{
    /**
     * @param $optionalParams
     * @return array
     * @throws ResponseException
     */
    public function parse($optionalParams)
    {
        $result = [];
        if (empty($optionalParams)) {
            return $result;
        }

        try {
            $options = array_merge(OptionalParams::OPTIONAL_PARAMS_BF_INSERT, $optionalParams);

            $capacity = $options[OptionalParams::CAPACITY];
            if (!is_null($capacity)) {
                $result[] = OptionalParams::CAPACITY;
                $result[] = (int)$capacity;
            }

            $error = $options[OptionalParams::ERROR];
            if (!is_null($error)) {
                $error = (float)$error;
                if ($error <= 0.0 || $error > 1.0) {
                    throw new InvalidArgumentException(
                        sprintf("option %s must be >= 0.0 and >= 1.0, provided value %f", OptionalParams::ERROR, $error)
                    );
                }
                $result[] = OptionalParams::ERROR;
                $result[] = (float)$error;
            }

            $noCreate = $options[OptionalParams::NOCREATE];
            if (!is_null($noCreate) && true === $noCreate) {
                $result[] = OptionalParams::NOCREATE;
            }
        } catch (Throwable $err) {
            throw new ResponseException($err->getMessage() . ', parsing optional params');
        }

        return $result;
    }
}
