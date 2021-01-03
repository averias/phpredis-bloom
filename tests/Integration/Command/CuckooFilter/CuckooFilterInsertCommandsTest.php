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
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class CuckooFilterInsertCommandsTest extends BaseTestIntegration
{
    /**
     * @dataProvider getInsertSuccessWithNoOptionsDataProvider
     * @param string $key
     * @param array $items
     * @param array $options
     * @param array $expectedResult
     */
    public function testInsertSuccessfullyWithNoOptions(
        string $key,
        array $items,
        array $options,
        array $expectedResult
    ): void {
        $result = static::$reBloomClient->cuckooFilterInsert($key, $items, $options);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getInsertIfNotExistsSuccessWithNoOptionsDataProvider
     * @param string $key
     * @param array $items
     * @param array $options
     * @param array $expectedResult
     */
    public function testInsertIfNotExistsSuccessfullyWithNoOptions(
        string $key,
        array $items,
        array $options,
        array $expectedResult
    ): void {
        $result = static::$reBloomClient->cuckooFilterInsertIfNotExist($key, $items, $options);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getInsertSuccessWithOptionsDataProvider
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
        $result = static::$reBloomClient->cuckooFilterInsert($key, $items, $options);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getInsertIfNotExistsSuccessWithOptionsDataProvider
     * @param string $key
     * @param array $items
     * @param array $options
     * @param array $expectedResult
     */
    public function testInsertIfNotExistsSuccessfullyWithOptions(
        string $key,
        array $items,
        array $options,
        array $expectedResult
    ): void {
        $result = static::$reBloomClient->cuckooFilterInsertIfNotExist($key, $items, $options);
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
        static::$reBloomClient->cuckooFilterInsert($key, $items, $options);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param array $items
     * @param array $options
     */
    public function testInsertIfNotExistsException(string $key, array $items, array $options): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterInsertIfNotExist($key, $items, $options);
    }

    public function getInsertSuccessWithNoOptionsDataProvider(): array
    {
        return [
            ['key-insert11', [67, 'bar'], [], [true, true]],
            ['key-insert12', [8.13, 7, 9.3, 'foo'], [], [true, true, true, true]],
            ['key-insert13', ['bar', 9.3], [], [true, true]],
            ['key-insert14', [67], [], [true]],
            ['key-insert15', [02471, 0b10100111001], [], [true, true]], // both values are converter to integer 1337
            ['key-insert16', ['bar', 'baz', 'foo'], [], [true, true, true]]
        ];
    }

    public function getInsertIfNotExistsSuccessWithNoOptionsDataProvider(): array
    {
        return [
            ['key-insert51', [37, 'bar'], [], [true, true]],
            ['key-insert51', [5.13, 7, 9.3, 'foo'], [], [true, true, true, true]],
            ['key-insert51', ['bar', 9.3], [], [false, false]],
            ['key-insert51', [37], [], [false]],
            ['key-insert51', [02471, 0b10100111001], [], [true, false]], // both values are converter to integer 1337
            ['key-insert51', ['bar', 'baz', 'foo'], [], [false, true, false]]
        ];
    }

    public function getInsertSuccessWithOptionsDataProvider(): array
    {
        return [
            [ // CAPACITY is ignored since the filter already exists but insertion is success
                'key-insert16',
                [12, 'bar'],
                [OptionalParams::CAPACITY => 1000],
                [true, true]
            ],
            [ // since the filter already exists CAPICITY is ignored, NO_CREATE is ok and insertion is success
                'key-insert16',
                [7.01, 9, 89.3, 'bar'],
                [OptionalParams::CAPACITY => 1000, OptionalParams::NO_CREATE => true],
                [true, true, true, true]
            ],
            [ // filter is created with CAPACITY = 10000 and since NO_CREATE is false, insertion doesn't fail
                'key-insert2',
                ['foo', 9],
                [OptionalParams::CAPACITY => 10000, OptionalParams::ERROR => 0.1, OptionalParams::NO_CREATE => false],
                [true, true]
            ],
            [ // filter is created with CAPACITY = 10000 and since NO_CREATE is omitted, insertion doesn't fail
                'key-insert3',
                ['foo', 9],
                [OptionalParams::CAPACITY => 1000],
                [true, true]
            ]
        ];
    }

    public function getInsertIfNotExistsSuccessWithOptionsDataProvider(): array
    {
        return [
            [ // CAPACITY is ignored since the filter already exists but insertion is success
                'key-insert16',
                [1200, 'green'],
                [OptionalParams::CAPACITY => 1000],
                [true, true]
            ],
            [ // since the filter already exists CAPICITY is ignored, NO_CREATE is ok and insertion is success
                'key-insert16',
                [1200, 1212, 118.93, 'green'],
                [OptionalParams::CAPACITY => 1000, OptionalParams::NO_CREATE => true],
                [false, true, true, false]
            ],
            [ // filter is created with CAPACITY = 10000 and since NO_CREATE is false, insertion doesn't fail
                'key-insert4',
                ['foo', 9],
                [OptionalParams::CAPACITY => 10000, OptionalParams::ERROR => 0.1, OptionalParams::NO_CREATE => false],
                [true, true]
            ],
            [ // filter is created with CAPACITY = 10000 and since NO_CREATE is omitted, insertion doesn't fail
                'key-insert5',
                ['foo', 9],
                [OptionalParams::CAPACITY => 1000],
                [true, true]
            ]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            [ // fails cause with NO_CREATE filter must exist
                'key-insert6',
                [1, 2],
                [OptionalParams::NO_CREATE => true]
            ],
            [ // fails cause wrong items to insert
                'key-insert6',
                ['foo', true],
                [OptionalParams::CAPACITY => 10000]
            ],
            [ // fails cause CAPACITY is not integer
                'key-insert6',
                [12, 13],
                [OptionalParams::CAPACITY => 'foo'],
            ]
        ];
    }
}
