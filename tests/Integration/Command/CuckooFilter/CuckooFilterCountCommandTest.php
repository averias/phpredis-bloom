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

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class CuckooFilterCountCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->cuckooFilterInsert(Keys::DEFAULT_KEY, ['foo', 13.4, 1337, 'foo', 0b10100111001, 02471]);
    }

    /**
     * @dataProvider getCountDataProvider
     * @param string $key
     * @param $item
     * @param int $expectedResult
     */
    public function testCountItem(string $key, $item, int $expectedResult): void
    {
        $result = static::$reBloomClient->cuckooFilterCount($key, $item);
        $this->assertEquals($result, $expectedResult);
    }

    public function testNonExistentKey(): void
    {
        $result = static::$reBloomClient->cuckooFilterCount('nonexistent-key', 'bar');
        $this->assertEquals(0, $result);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param $item
     */
    public function testExistsItemException(string $key, $item): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterCount($key, $item);
    }

    public function getCountDataProvider(): array
    {
        return [
            [Keys::DEFAULT_KEY, 'foo', 2],
            [Keys::DEFAULT_KEY, 13.4, 1],
            [Keys::DEFAULT_KEY, 1337, 3]
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
