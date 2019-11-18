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

namespace Averias\RedisBloom\Tests\Integration\Command\TopK;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class TopKAddCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->topKReserve('key-add1', 2, 100, 3, 0.95);
        static::$reBloomClient->topKReserve('key-add2', 2, 100, 3, 0.95);
    }

    /**
     * @dataProvider getSuccessDataProvider
     * @param string $key
     * @param array $items
     * @param array $expectedResult
     */
    public function testAddSuccessfully(string $key, array $items, array $expectedResult): void
    {
        $result = static::$reBloomClient->topKAdd($key, ...$items);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $items
     */
    public function testAddException(string $key, array $items): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->topKAdd($key, ...$items);
    }

    public function getSuccessDataProvider(): array
    {
        return [
            ['key-add1', [12, 'bar'], [false, false]],
            ['key-add1', [7.01, 9, 89.3, 'bar'], [false, false, false, false]],
            ['key-add1', ['foo', 9], [false, "12"]],
            ['key-add1', [12, "12", 12], [false, 'bar', false]],
            ['key-add1', [02471, 0b10100111001, 1337, "1337"], [false, false, "9", false]],
            ['key-add1', ['bar', 'bar', 'bar'], [false, false, "12"]]
        ];
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-add2', [[1, 2]]],
            ['key-add2', [true, 'foo']],
            ['key-add2', [false]],
            ['key-add2', []],
            ['key-add3', [12, 'bar']] // top-k doesn't exist
        ];
    }
}
