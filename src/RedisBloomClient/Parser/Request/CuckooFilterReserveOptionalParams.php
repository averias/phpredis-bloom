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

class CuckooFilterReserveOptionalParams implements ParserInterface
{
    /**
     * @param array $optionalParams
     * @return array
     * @throws ResponseException
     */
    public function parse($optionalParams)
    {
        $result = [];
        if (empty($optionalParams)) {
            return $result;
        }

        $options = array_merge(OptionalParams::OPTIONAL_PARAMS_CF_RESERVE, $optionalParams);
        $optionsKeys = array_keys(OptionalParams::OPTIONAL_PARAMS_CF_RESERVE);

        foreach ($optionsKeys as $optionsKey) {
            $optionValue = $options[$optionsKey];
            if (!is_null($optionValue)) {
                if (!is_int($optionValue)) {
                    throw new ResponseException(sprintf("option %s must be integer", $optionsKey));
                }
                $result[] = $optionsKey;
                $result[] = $optionValue;
            }
        }

        return $result;
    }
}
