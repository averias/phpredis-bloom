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

namespace Averias\RedisBloom\Parser\Response;

use Averias\RedisBloom\Parser\ParserInterface;

class ArrayOfIntegerToBool extends BaseResponseParser implements ParserInterface
{
    /**
     * @param $response
     * @return mixed
     */
    public function parse($response)
    {
        foreach ($response as $key => $item) {
            $response[$key] = $this->convertIntegerToBool($item);
        }

        return $response;
    }
}
