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

class TopKListCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->topKReserve('key-list1', 2, 100, 3, 0.95);
        static::$reBloomClient->topKReserve('key-list2', 2, 100, 3, 0.95);
    }

    /**
     * @dataProvider getSuccessDataProvider
     * @param string $key
     * @param array $items
     * @param array $expectedResult
     */
    public function testListSuccessfully(string $key, array $items, array $expectedResult): void
    {
        static::$reBloomClient->topKIncrementBy($key, ...$items);
        $result = static::$reBloomClient->topKList($key);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $items
     */
    public function testListException(string $key, array $items): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->topKAdd($key, ...$items);
    }

    public function getSuccessDataProvider(): array
    {
        return [
            ['key-list1', [12, 12, 'bar', 5], ['bar', '12']],
            ['key-list1', ["12", 12, 'foo', 17], ['foo', '12']],
            ['key-list1', ['foo', 9], ['12', 'foo']],
            ['key-list1', ['baz', 25, "bar", 20], ['baz', 'foo']],
            ['key-list1', [12, 2, 'baz', 2], ['12', 'baz']]
        ];
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-list2', [[1, 2]]],
            ['key-list2', [true, 'foo']],
            ['key-list2', [false]],
            ['key-list2', []],
            ['key-list3', [12, 'bar']] // top-k key doesn't exist
        ];
    }
}
