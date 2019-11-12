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

namespace Averias\RedisBloom\Parser\Response;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\ParserInterface;

class OkToTrue implements ParserInterface
{
    /**
     * @param $response
     * @return bool
     * @throws ResponseException
     */
    public function parse($response): bool
    {
        if (!is_string($response)) {
            throw new ResponseException(sprintf("expected string response but got '%s'", gettype($response)));
        }

        if ($response !== 'OK') {
            throw new ResponseException(sprintf("expected 'OK' string response but got '%s'", $response));
        }

        return true;
    }
}
