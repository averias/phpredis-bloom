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

class BloomFilterReserveCommandTest extends BaseTestIntegration
{
    /**
     * @dataProvider getDataProvider
     * @param string $key
     * @param float $errorRate
     * @param int $capacity
     */
    public function testSuccessReservation(string $key, float $errorRate, int $capacity)
    {
        $result = static::$reBloomClient->bloomFilterReserve($key, $errorRate, $capacity);
        $this->assertTrue($result);
    }

    public function testReservationException()
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterReserve('key-reserve1', 0.1, 1000);
    }

    public function getDataProvider()
    {
        return [
            ['key-reserve1', 0.1, 10000],
            ['key-reserve2', 0.01, 1000000],
            ['key-reserve3', 0.001, 10000000],
            ['key-reserve4', 0.0001, 100000000],
            ['key-reserve5', 0.00001, 1000000000]
        ];
    }
}
