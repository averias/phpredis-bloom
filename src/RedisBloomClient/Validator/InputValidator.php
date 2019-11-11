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

namespace Averias\RedisBloom\Validator;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Exception\ResponseException;

class InputValidator implements InputValidatorInterface
{
    /**
     * @param $value
     * @throws ResponseException
     */
    public function validateScalar($value): void
    {
        if (!is_string($value) && !is_numeric($value)) {
            throw new ResponseException(
                sprintf("value for parameter item in command %s must be string or number", BloomCommands::BF_MADD)
            );
        }
    }
}
