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

class TopKReserveCommandTest extends BaseTestIntegration
{
    /**
     * @dataProvider getDataProvider
     * @param $key
     * @param $topK
     * @param $width
     * @param $depth
     * @param $decay
     */
    public function testSuccessReservation($key, $topK, $width, $depth, $decay): void
    {
        $result = static::$reBloomClient->topKReserve($key, $topK, $width, $depth, $decay);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param $key
     * @param $topK
     * @param $width
     * @param $depth
     * @param $decay
     */
    public function testReservationException($key, $topK, $width, $depth, $decay): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->topKReserve($key, $topK, $width, $depth, $decay);
    }

    public function getDataProvider(): array
    {
        return [
            ['key-reserve1', 3, 100, 3, 0.95],
            ['key-reserve2', 5, 500, 4, 1.0],
            ['key-reserve3', 7, 1500, 8, 0.15]
        ];
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-reserve1', 3, 100, 3, 0.95], // topK key exists
            ['key-reserve4', 5, 500, 4, 1.00001] // decay must be > 0 and <= 1
        ];
    }
}
