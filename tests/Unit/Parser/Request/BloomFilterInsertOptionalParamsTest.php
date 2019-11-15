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

namespace Averias\RedisBloom\Tests\Parser\Request;

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
     * @param string $exceptionClassName
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
                [OptionalParams::NOCREATE => true],
                [OptionalParams::NOCREATE]
            ],
            [
                [OptionalParams::NOCREATE => false],
                []
            ],
            [
                [OptionalParams::NOCREATE => null],
                []
            ],
            [
                [OptionalParams::NOCREATE => 23],
                []
            ],
            [
                [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.1],
                [OptionalParams::CAPACITY, 100, OptionalParams::ERROR, 0.1]
            ],
            [
                [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.1, OptionalParams::NOCREATE => true],
                [OptionalParams::CAPACITY, 100, OptionalParams::ERROR, 0.1, OptionalParams::NOCREATE]
            ],
            [
                [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.1, OptionalParams::NOCREATE => false],
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
