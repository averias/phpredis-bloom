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

use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class BloomFilterInsertCommandTest extends BaseTestIntegration
{
    /**
     * @dataProvider getSuccessWithNoOptionsDataProvider
     * @param string $key
     * @param array $items
     * @param array $expectedResult
     */
    public function testInsertSuccessfullyWithNoOptions(string $key, array $items, array $expectedResult): void
    {
        $result = static::$reBloomClient->bloomFilterInsert($key, $items);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getSuccessWithOptionsDataProvider
     * @param string $key
     * @param array $items
     * @param array $options
     * @param array $expectedResult
     */
    public function testInsertSuccessfullyWithOptions(
        string $key,
        array $items,
        array $options,
        array $expectedResult
    ): void {
        $result = static::$reBloomClient->bloomFilterInsert($key, $items, $options);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param array $items
     * @param array $options
     */
    public function testInsertException(string $key, array $items, array $options): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterInsert($key, $items, $options);
    }

    public function getSuccessWithNoOptionsDataProvider(): array
    {
        return [
            ['key-insert11', [12, 'bar'], [true, true]],
            ['key-insert12', [7.01, 9, 89.3, 'bar'], [true, true, true, true]],
            ['key-insert13', ['foo', 9], [true, true]],
            ['key-insert14', [12], [true]],
            ['key-insert15', [02471, 0b10100111001], [true, false]], // both values are converter to integer 1337
            ['key-insert16', ['bar', 'baz', 'foo'], [true, true, true]]
        ];
    }

    public function getSuccessWithOptionsDataProvider(): array
    {
        return [
            [
                'key-insert2',
                [12, 'bar'],
                [OptionalParams::CAPACITY => 1000],
                [true, true]
            ],
            [
                'key-insert3',
                [7.01, 9, 89.3, 'bar'],
                [OptionalParams::CAPACITY => 1000, OptionalParams::ERROR => 0.01],
                [true, true, true, true]
            ],
            [ // CAPACITY and ERROR are no changed since the filter already exists
                'key-insert3',
                ['foo', 9],
                [OptionalParams::CAPACITY => 10000, OptionalParams::ERROR => 0.1, OptionalParams::NO_CREATE => true],
                [true, false]
            ],
            [ // CAPACITY and EXPANSION are no changed since the filter already exists
                'key-insert3',
                ['baz', 19],
                [OptionalParams::CAPACITY => 10000, OptionalParams::EXPANSION => 10, OptionalParams::NO_CREATE => true],
                [true, true]
            ],
            [
                'key-insert4',
                ['foo', 9],
                [OptionalParams::CAPACITY => 1000, OptionalParams::ERROR => 0.01, OptionalParams::NO_CREATE => false],
                [true, true]
            ],
            // doesn't fail cause NO_CREATE is ignored if not true, so 'key-insert5' is created since it doesn't exist
            [
                'key-insert5',
                ['foo', 9],
                [OptionalParams::CAPACITY => 1000, OptionalParams::ERROR => 0.01, OptionalParams::NO_CREATE => false],
                [true, true]
            ],
            // doesn't fail even when NON_SCALING is a wrong value, NON_SCALING it is ignored if not true
            [
                'key-insert6',
                ['foo', 9],
                [OptionalParams::CAPACITY => 1000, OptionalParams::ERROR => 0.01, OptionalParams::NON_SCALING => 3],
                [true, true]
            ]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            [ // fails cause with NO_CREATE filter must exist
                'key-insert51',
                [1, 2],
                [OptionalParams::NO_CREATE => true]
            ],
            [ // fails cause wrong items to insert
                'key-insert7',
                [true, 'foo'],
                [OptionalParams::CAPACITY => 10000]
            ],
            [ // fails cause wrong items to insert
                'key-insert7',
                [false],
                [OptionalParams::ERROR => 0.1]
            ],
            [ // fails cause ERROR must be > 0.0 and < 1.0
                'key-insert7',
                [5, 6],
                [OptionalParams::ERROR => 2]
            ],
            [ // fails cause ERROR must be > 0.0 and < 1.0
                'key-insert7',
                [5, 6],
                [OptionalParams::ERROR => 0]
            ],
            [ // fails cause ERROR must be > 0.0 and < 1.0
                'key-insert7',
                [5, 6],
                [OptionalParams::ERROR => 1.0]
            ],
            [ // fails cause CAPACITY is not integer
                'key-insert7',
                [12, 13],
                [OptionalParams::CAPACITY => 'foo'],
            ],
            [ // fails ERROR is not float
                'key-insert7',
                [22, 33],
                [OptionalParams::ERROR => 'foo'],
            ]
            ,
            [ // fails ERROR is not float
                'key-insert7',
                [22, 33],
                [OptionalParams::ERROR => 1],
            ],
            [ // fails EXPANSION is not integer
                'key-insert7',
                [22, 33],
                [OptionalParams::EXPANSION => 'foo'],
            ]
            ,
            [ // fails EXPANSION is not integer
                'key-insert7',
                [22, 33],
                [OptionalParams::EXPANSION => 1.3],
            ]
        ];
    }
}
