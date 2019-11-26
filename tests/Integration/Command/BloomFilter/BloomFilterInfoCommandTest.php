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

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class BloomFilterInfoCommandTest extends BaseTestIntegration
{
    public function testSuccessfulInfo(): void
    {
        static::$reBloomClient->bloomFilterReserve('key-info1', 0.1, 100, [OptionalParams::EXPANSION => 4]);
        static::$reBloomClient->bloomFilterMultiAdd('key-info1', 'blue', 'red', 'yellow', 'purple');

        $info1 = static::$reBloomClient->bloomFilterInfo('key-info1');
        $this->assertArrayHasKey(Keys::CAPACITY, $info1);
        $this->assertArrayHasKey(Keys::SIZE, $info1);
        $this->assertEquals(1, $info1[Keys::NUMBER_FILTERS]);
        $this->assertEquals(4, $info1[Keys::NUMBER_ITEMS_INSERTED]);
        $this->assertEquals(4, $info1[Keys::EXPANSION_RATE]);

        static::$reBloomClient->bloomFilterMultiAdd('key-info1', 'orange', 'black');

        $info1 = static::$reBloomClient->bloomFilterInfo('key-info1');
        $this->assertEquals(6, $info1[Keys::NUMBER_ITEMS_INSERTED]);
    }

    public function testInfoException(): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->bloomFilterInfo('key-info2');
    }
}
