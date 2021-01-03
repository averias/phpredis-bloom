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
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class TopKCountCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->topKReserve('key-count1', 2, 100, 3, 0.95);
        static::$reBloomClient->topKReserve('key-count2', 2, 100, 3, 0.95);
    }

    public function testSuccessfulCount(): void
    {
        static::$reBloomClient->topKIncrementBy('key-count1', 12, 10, 'foo', 15, 'bar', 13, '1337', 25);
        static::$reBloomClient->topKIncrementBy('key-count1', '12', 55, 'foo', 1, 'bar', 7, 1337, 25);
        $query1 = static::$reBloomClient->topKCount('key-count1', 12, 'foo', 'bar', '1337', 'baz');

        $this->assertEquals(65, $query1[0]);
        $this->assertEquals(16, $query1[1]);
        $this->assertEquals(20, $query1[2]);
        $this->assertEquals(50, $query1[3]);
        $this->assertEquals(0, $query1[4]); // 'baz' doesn't exist
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $arguments
     */
    public function testCountException($key, $arguments): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->topKCount($key, ...$arguments);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-count2', [true, 44]], // wrong boolean param
            ['key-count2', [['foo'], true]], // wrong array param
            ['key-count3', ['foo', 4, 'bar']] // non-existent key
        ];
    }
}
