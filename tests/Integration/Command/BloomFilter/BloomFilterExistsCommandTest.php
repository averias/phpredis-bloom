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

class BloomFilterExistsCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->bloomFilterMultiAdd(Keys::DEFAULT_KEY, 'foo', 13.4, 1337);
    }

    /**
     * @dataProvider getExistsDataProvider
     * @param string $key
     * @param $item
     */
    public function testExistsItem(string $key, $item): void
    {
        $result = static::$reBloomClient->bloomFilterExists($key, $item);
        $this->assertTrue($result);
    }

    public function testDoesntExistItem(): void
    {
        $result = static::$reBloomClient->bloomFilterExists(Keys::DEFAULT_KEY, 'bar');
        $this->assertFalse($result);
    }

    public function testNonExistentKey(): void
    {
        $result = static::$reBloomClient->bloomFilterExists('nonexistent-key', 'bar');
        $this->assertFalse($result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param $item
     */
    public function testExistsItemException(string $key, $item): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterExists($key, $item);
    }

    public function getExistsDataProvider(): array
    {
        return [
            [Keys::DEFAULT_KEY, 'foo'],
            [Keys::DEFAULT_KEY, 13.4],
            [Keys::DEFAULT_KEY, 1337],
            [Keys::DEFAULT_KEY, 02471],
            [Keys::DEFAULT_KEY, 0b10100111001]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            [Keys::DEFAULT_KEY, [[1, 2]]],
            [Keys::DEFAULT_KEY, true]
        ];
    }
}
