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
use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CuckooFilterInfoCommandTest extends BaseTestIntegration
{
    public function testSuccessfulInfo(): void
    {
        static::$reBloomClient->cuckooFilterReserve(
            'key-info1',
            200,
            [OptionalParams::BUCKET_SIZE => 100, OptionalParams::MAX_ITERATIONS => 5, OptionalParams::EXPANSION => 3]
        );

        static::$reBloomClient->cuckooFilterInsert('key-info1', ['blue', 'red', 'yellow', 'purple', 'black']);

        $info1 = static::$reBloomClient->cuckooFilterInfo('key-info1');
        $this->assertArrayHasKey(Keys::SIZE, $info1);
        $this->assertArrayHasKey(Keys::NUMBER_BUCKETS, $info1);
        $this->assertArrayHasKey(Keys::EXPANSION_RATE, $info1);
        $this->assertEquals(1, $info1[Keys::NUMBER_FILTERS]);
        $this->assertEquals(5, $info1[Keys::NUMBER_ITEMS_INSERTED]);
        $this->assertEquals(0, $info1[Keys::NUMBER_ITEMS_DELETED]);
        $this->assertEquals(100, $info1[Keys::BUCKET_SIZE]);
        $this->assertEquals(5, $info1[Keys::MAX_ITERATIONS]);

        static::$reBloomClient->cuckooFilterInsert('key-info1', ['orange', 'black']);

        $info1 = static::$reBloomClient->cuckooFilterInfo('key-info1');
        $this->assertEquals(7, $info1[Keys::NUMBER_ITEMS_INSERTED]);
    }

    public function testInfoException(): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterInfo('key-info2');
    }
}
