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

namespace Averias\RedisBloom\Tests\Unit\Parser\Request;

use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\Request\BloomFilterInsertOptionalParams;
use PHPUnit\Framework\TestCase;

class BloomFilterInsertOptionalParamsTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     * @param array $requestParams
     * @param array $expectParsedRequest
     */
    public function testParse(array $requestParams, array $expectParsedRequest)
    {
        $parser = new BloomFilterInsertOptionalParams();
        $parsedRequest = $parser->parse($requestParams);
        $this->assertEquals($expectParsedRequest, $parsedRequest);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param array $requestParams
     */
    public function testParseException(array $requestParams)
    {
        $this->expectException(ResponseException::class);
        $parser = new BloomFilterInsertOptionalParams();
        $parser->parse($requestParams);
    }

    public function getDataProvider(): array
    {
        return [
            [
                [],
                []
            ],
            [
                [OptionalParams::CAPACITY => 100],
                [OptionalParams::CAPACITY, 100]
            ],
            [
                [OptionalParams::ERROR => 0.1],
                [OptionalParams::ERROR, 0.1]
            ],
            [
                [OptionalParams::NO_CREATE => true],
                [OptionalParams::NO_CREATE]
            ],
            [
                [OptionalParams::NO_CREATE => false],
                []
            ],
            [
                [OptionalParams::NO_CREATE => null],
                []
            ],
            [
                [OptionalParams::NO_CREATE => 23],
                []
            ],
            [
                [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.1],
                [OptionalParams::CAPACITY, 100, OptionalParams::ERROR, 0.1]
            ],
            [
                [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.1, OptionalParams::NO_CREATE => true],
                [OptionalParams::CAPACITY, 100, OptionalParams::ERROR, 0.1, OptionalParams::NO_CREATE]
            ],
            [
                [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.1, OptionalParams::NO_CREATE => false],
                [OptionalParams::CAPACITY, 100, OptionalParams::ERROR, 0.1]
            ]
        ];
    }

    public function getExceptionDataProvider(): array
    {
        return [
            [[OptionalParams::ERROR => 0.0]],
            [[OptionalParams::ERROR => 2.0]],
            [[OptionalParams::ERROR => 'foo']],
            [[OptionalParams::ERROR => 1]],
            [[OptionalParams::CAPACITY => 'foo']],
            [[OptionalParams::CAPACITY => 1.5]]
        ];
    }
}
