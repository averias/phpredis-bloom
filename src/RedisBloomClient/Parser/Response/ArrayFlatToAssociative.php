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

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\ParserInterface;
use Averias\RedisBloom\Validator\InputValidatorTrait;

class ArrayFlatToAssociative extends BaseResponseParser implements ParserInterface
{
    use InputValidatorTrait;

    /**
     * @param $response
     * @return array|false
     * @throws ResponseException
     */
    public function parse($response)
    {
        if (!is_array($response)) {
            throw new ResponseException(sprintf("expected array response but got '%s'", gettype($response)));
        }

        $responseLength = count($response);
        if ($responseLength < 2) {
            throw new ResponseException(sprintf("expected an array response length >= 2 but got %d", $responseLength));
        }

        $this->validateEvenArrayDimension($response, 'response');

        // get elements in odd indexes which will be the keys in the returned array
        $odd = $this->getElementsInOddIndexes($response);
        $odd = $this->convertArrayKeysToString($odd);

        // get elements in even indexes which will be the values in the returned array
        $even = $this->getElementsInEvenIndexes($response);

        return array_combine($odd, $even);
    }

    protected function getElementsInOddIndexes($response): array
    {
        return array_filter(
            $response,
            function ($key) {
                return $key % 2 == 0;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected function getElementsInEvenIndexes($response): array
    {
        return array_filter(
            $response,
            function ($key) {
                return $key % 2 == 1;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected function convertArrayKeysToString(array $array): array
    {
        return array_map(
            function ($value) {
                return (string) $value;
            },
            $array
        );
    }
}
