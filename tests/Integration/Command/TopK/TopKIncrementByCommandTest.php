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

namespace Averias\RedisBloom\Tests\Integration\Command\TopK;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class TopKIncrementByCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->topKReserve('key-incr1', 2, 100, 3, 0.95);
        static::$reBloomClient->topKReserve('key-incr2', 2, 100, 3, 0.95);
    }

    /**
     * @dataProvider getSuccessDataProvider
     * @param string $key
     * @param array $items
     * @param array $expectedResult
     */
    public function testIncrementSuccessfully(string $key, array $items, array $expectedResult): void
    {
        $result = static::$reBloomClient->topKIncrementBy($key, ...$items);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $arguments
     */
    public function testInitException($key, $arguments): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->topKIncrementBy($key, ...$arguments);
    }

    public function getSuccessDataProvider(): array
    {
        return [
            ['key-incr1', [12, 12, 'bar', 5], [false, false]],
            ['key-incr1', ["12", 12, 'foo', 17], [false, 'bar']],
            ['key-incr1', ['foo', 9], [false]],
            ['key-incr1', ['baz', 25, "bar", 20], ['12', false]],
            ['key-incr1', [12, 2, 'baz', 2], ['baz', 'foo']]
        ];
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-incr2', [true, 44]], // wrong item
            ['key-incr2', ['bar', true]], // wrong increment
            ['key-incr2', ['foo', 4, 'bar']], // incomplete item-increment pairs
            ['key-incr2', ['foo', 4.3]], //wrong float increment
            ['key-incr3', ['baz', 10]] // non-existent key
        ];
    }
}
