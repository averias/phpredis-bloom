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

class CuckooFilterInsertOptionalParams extends BaseRequestOptionalParams implements ParserInterface
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
            $options = $this->getMergedOptionalParams(OptionalParams::OPTIONAL_PARAMS_CF_INSERT, $optionalParams);

            $result = $this->appendCapacity($result, $options);
            $result = $this->appendNoCreate($result, $options);
        } catch (Throwable $err) {
            throw new ResponseException($err->getMessage() . ', parsing optional params');
        }

        return $result;
    }
}
