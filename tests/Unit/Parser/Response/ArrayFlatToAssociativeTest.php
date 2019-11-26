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

namespace Averias\RedisBloom\Tests\Unit\Parser\Response;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\Response\ArrayFlatToAssociative;
use PHPUnit\Framework\TestCase;

class ArrayFlatToAssociativeTest extends TestCase
{
    public function testResponseIsNotArrayException(): void
    {
        $this->expectException(ResponseException::class);
        $parser = new ArrayFlatToAssociative();
        $parser->parse(23);
    }

    public function testResponseArrayIsShortyException(): void
    {
        $this->expectException(ResponseException::class);
        $parser = new ArrayFlatToAssociative();
        $parser->parse(['foo']);
    }

    public function testResponseArrayLengthIsNotEvenException(): void
    {
        $this->expectException(ResponseException::class);
        $parser = new ArrayFlatToAssociative();
        $parser->parse(['foo', 13, 'bar']);
    }

    /**
     * @dataProvider getDataProvider
     * @param array $response
     * @param array $expectedResponse
     * @throws ResponseException
     */
    public function testParse(array $response, array $expectedResponse): void
    {
        $parser = new ArrayFlatToAssociative();
        $parsedResponse = $parser->parse($response);
        $this->assertEquals($expectedResponse, $parsedResponse);
    }

    public function getDataProvider(): array
    {
        return [
            [range(1, 6), ['1' => 2, '3' => 4, '5' => 6]],
            [['foo', 'bar', 'baz', 18, 116, 115], ['foo' => 'bar', 'baz' => 18, '116' => 115]],
            [['foo', 54, 'bar', 18, 'baz', 115], ['foo' => 54, 'bar' => 18, 'baz' => 115]]
        ];
    }
}
