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

namespace Averias\RedisBloom\Tests\Integration\Command\CuckooFilter;

use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CuckooFilterReserveCommandTest extends BaseTestIntegration
{
    /**
     * @dataProvider getSuccessWithNoOptionsDataProvider
     * @param string $key
     * @param int $capacity
     */
    public function testSuccessReservationWithNoOptions(string $key, int $capacity): void
    {
        $result = static::$reBloomClient->cuckooFilterReserve($key, $capacity);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getSuccessWithOptionsDataProvider
     * @param string $key
     * @param int $capacity
     * @param array $options
     */
    public function testSuccessReservationWithOptions(string $key, int $capacity, array $options): void
    {
        $result = static::$reBloomClient->cuckooFilterReserve($key, $capacity, $options);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param int $capacity
     * @param array $options
     */
    public function testReserveException(string $key, int $capacity, array $options): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterReserve($key, $capacity, $options);
    }

    public function getSuccessWithNoOptionsDataProvider(): array
    {
        return [
            ['key-reserve11', 100],
            ['key-reserve12', 1000]
        ];
    }

    public function getSuccessWithOptionsDataProvider(): array
    {
        return [
            [
                'key-reserve21',
                100,
                [OptionalParams::BUCKET_SIZE => 10]
            ],
            [
                'key-reserve22',
                100,
                [OptionalParams::BUCKET_SIZE => 10, OptionalParams::MAX_ITERATIONS => 5]
            ],
            [
                'key-reserve23',
                100,
                [OptionalParams::BUCKET_SIZE => 10, OptionalParams::MAX_ITERATIONS => 3, OptionalParams::EXPANSION => 5]
            ]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            [ // fails cause filter already exists
                'key-reserve23',
                100,
                []
            ],
            [ // fails cause BUCKET_SIZE is not integer
                'key-reserve3',
                10,
                [OptionalParams::BUCKET_SIZE => 'foo'],
            ],
            [ // fails cause MAX_ITERATIONS is not integer
                'key-reserve3',
                10,
                [OptionalParams::MAX_ITERATIONS => 'foo'],
            ],
            [ // fails cause EXPANSION is not integer
                'key-reserve3',
                10,
                [OptionalParams::EXPANSION => 1.2],
            ]
        ];
    }
}
