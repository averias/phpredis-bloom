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

namespace Averias\RedisBloom\Tests\Integration\Command\BloomFilter;

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class BloomFilterMultiExistsCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->bloomFilterMultiAdd(Keys::DEFAULT_KEY, 'foo', 12, 9, 1337);
    }

    /**
     * @dataProvider getExistsDataProvider
     * @param string $key
     * @param array $items
     * @param array $expectedResult
     */
    public function testMultiExists(string $key, array $items, array $expectedResult): void
    {
        $result = static::$reBloomClient->bloomFilterMultiExists($key, ...$items);
        $this->assertSame($expectedResult, $result);
    }

    public function testNonExistentKey(): void
    {
        $result = static::$reBloomClient->bloomFilterMultiExists('nonexistent-key', 'bar', 'foo');
        $this->assertSame([false, false], $result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param array $items
     */
    public function testMultiExistsException(string $key, array $items): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterMultiExists($key, ...$items);
    }

    public function getExistsDataProvider(): array
    {
        return [
            [Keys::DEFAULT_KEY, [12, 'bar'], [true, false]],
            [Keys::DEFAULT_KEY, [7.01, 9, 89.3, 'bar'], [false, true, false, false]],
            [Keys::DEFAULT_KEY, ['foo', 9], [true, true]],
            [Keys::DEFAULT_KEY, [12], [true]],
            [Keys::DEFAULT_KEY, [02471, 0b10100111001], [true, true]],
            [Keys::DEFAULT_KEY, ['bar', 'baz', 'foo'], [false, false, true]]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            [Keys::DEFAULT_KEY, [[1, 2]]],
            [Keys::DEFAULT_KEY, [true, 'foo']],
            [Keys::DEFAULT_KEY, [false]],
            [Keys::DEFAULT_KEY, []]
        ];
    }
}
