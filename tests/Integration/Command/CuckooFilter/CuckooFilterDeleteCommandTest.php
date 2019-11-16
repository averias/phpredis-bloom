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

namespace Averias\RedisBloom\Tests\Integration\Command\CuckooFilter;

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CuckooFilterDeleteCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->cuckooFilterInsert(Keys::DEFAULT_KEY, ['foo', 13.4, 1337, 'foo', 'bar']);
    }

    /**
     * @dataProvider getDeleteDataProvider
     * @param string $key
     * @param $item
     * @param bool $expectedResult
     */
    public function testDeleteItem(string $key, $item, bool $expectedResult): void
    {
        $result = static::$reBloomClient->cuckooFilterDelete($key, $item);
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @dataProvider getDataProviderForException
     * @param string $key
     * @param $item
     */
    public function testExistsItemException(string $key, $item): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterDelete($key, $item);
    }

    public function getDeleteDataProvider(): array
    {
        return [
            [Keys::DEFAULT_KEY, 'foo', true],
            [Keys::DEFAULT_KEY, 'foo', true],
            [Keys::DEFAULT_KEY, 13.4, true],
            [Keys::DEFAULT_KEY, 1337, true],
            [Keys::DEFAULT_KEY, 02471, false],
            [Keys::DEFAULT_KEY, 0b10100111001, false]
        ];
    }

    public function getDataProviderForException(): array
    {
        return [
            [Keys::DEFAULT_KEY, [[1, 2]]],
            [Keys::DEFAULT_KEY, true],
            ['non-existent-key', 'bar'] // trying to delete a item in a non existent key throws an exception
        ];
    }
}
