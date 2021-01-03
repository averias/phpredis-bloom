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

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class CuckooFilterAddCommandsTest extends BaseTestIntegration
{
    /**
     * @dataProvider getSuccessDataProvider
     * @param string $key
     * @param $item
     * @param bool $expectedResult
     */
    public function testAddItemSuccessfully(string $key, $item, bool $expectedResult): void
    {
        $result = static::$reBloomClient->cuckooFilterAdd($key, $item);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param $item
     */
    public function testAddItemException(string $key, $item): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterAdd($key, $item);
    }

    public function testAddIfNotExistItem()
    {
        $result = static::$reBloomClient->cuckooFilterAddIfNotExist('key-add-nx', 10);
        $this->assertTrue($result);
        $result = static::$reBloomClient->cuckooFilterAddIfNotExist('key-add-nx', 10);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param $item
     */
    public function testAddIfNotExistItemException(string $key, $item): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterAddIfNotExist($key, $item);
    }

    public function getSuccessDataProvider(): array
    {
        return [
            ['key-add1', 12, true],
            ['key-add1', 7.01, true],
            ['key-add1', 'foo', true],
            ['key-add1', 12, true],
            ['key-add1', 02471, true],
            ['key-add1', 0b10100111001, true],
            ['key-add1', 1337e0, true],
            ['key-add1', 0x539, true]
        ];
    }

    public function getDataProviderForException()
    {
        return [
            ['key-add1', [1, 2]],
            ['key-add1', true],
            ['key-add1', false]
        ];
    }
}
