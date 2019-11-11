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

namespace Averias\RedisBloom\Tests\Integration\Command\Traits\BloomFilter;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class BloomFilterAddCommandTest extends BaseTestIntegration
{
    /**
     * @dataProvider getSuccessDataProvider
     * @param string $key
     * @param $item
     * @param bool $expectedResult
     */
    public function testAddItemSuccessfully(string $key, $item, bool $expectedResult)
    {
        $result = static::$reBloomClient->bloomFilterAdd($key, $item);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param $item
     */
    public function testAddItemException(string $key, $item)
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterAdd($key, $item);
    }

    public function getSuccessDataProvider()
    {
        return [
            ['key-add1', 12, true],
            ['key-add1', 7.01, true],
            ['key-add1', 'foo', true],
            ['key-add1', 12, false],
            ['key-add1', 02471, true],
            ['key-add1', 0b10100111001, false],
            ['key-add1', 1337e0, false],
            ['key-add1', 0x539, false]
        ];
    }

    public function getDataProviderForException()
    {
        return [
            ['key-add1', [1, 2]],
            ['key-add1', true],
            ['key-add1', false]
        ];
    }
}
