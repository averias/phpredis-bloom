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

namespace Averias\RedisBloom\Tests\Integration\Command\BloomFilter;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class BloomFilterMultiAddCommandTest extends BaseTestIntegration
{
    /**
     * @dataProvider getSuccessDataProvider
     * @param string $key
     * @param array $items
     * @param array $expectedResult
     */
    public function testMultiAddItemSuccessfully(string $key, array $items, array $expectedResult): void
    {
        $result = static::$reBloomClient->bloomFilterMultiAdd($key, ...$items);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param array $items
     */
    public function testMultiAddItemException(string $key, array $items): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterMultiAdd($key, ...$items);
    }

    public function getSuccessDataProvider(): array
    {
        return [
            ['key-multi-add1', [12, 'bar'], [true, true]],
            ['key-multi-add1', [7.01, 9, 89.3, 'bar'], [true, true, true, false]],
            ['key-multi-add1', ['foo', 9], [true, false]],
            ['key-multi-add1', [12], [false]],
            ['key-multi-add1', [02471, 0b10100111001], [true, false]],
            ['key-multi-add1', ['bar', 'baz', 'foo'], [false, true, false]]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            ['key-multi-add1', [[1, 2]]],
            ['key-multi-add1', [true, 'foo']],
            ['key-multi-add1', [false]],
            ['key-multi-add1', []]
        ];
    }
}
