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

class TopKQueryCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->topKReserve('key-query1', 2, 100, 3, 0.95);
        static::$reBloomClient->topKReserve('key-query2', 2, 100, 3, 0.95);
    }

    public function testSuccessfulQuery(): void
    {
        static::$reBloomClient->topKIncrementBy('key-query1', 12, 10, 'foo', 15, 'bar', 13, '1337', 25);
        $query1 = static::$reBloomClient->topKQuery('key-query1', 12, 'foo', 'bar', '1337');

        $this->assertFalse($query1[0]);
        $this->assertTrue($query1[1]);
        $this->assertFalse($query1[2]);
        $this->assertTrue($query1[3]);

        static::$reBloomClient->topKIncrementBy('key-query1', '12', 55, 'foo', 1, 'bar', 7, 1337, 25);
        $query1 = static::$reBloomClient->topKQuery('key-query1', '12', 'foo', 'bar', 1337);

        $this->assertTrue($query1[0]);
        $this->assertFalse($query1[1]);
        $this->assertFalse($query1[2]);
        $this->assertTrue($query1[3]);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $arguments
     */
    public function testQueryException($key, $arguments): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->topKQuery($key, ...$arguments);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-query2', [true, 44]], // wrong boolean param
            ['key-query2', [['foo'], true]], // wrong array param
            ['key-query3', ['foo', 4, 'bar']] // non-existent key
        ];
    }
}
