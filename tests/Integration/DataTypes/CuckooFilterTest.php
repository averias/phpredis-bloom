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

namespace Averias\RedisBloom\Tests\Integration\DataTypes;

use Averias\RedisBloom\DataTypes\CuckooFilter;
use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CuckooFilterTest extends BaseTestIntegration
{
    /** @var CuckooFilter */
    protected static $cuckooFilter;

    /**
     * @throws RedisClientException
     */
    public static function setUpBeforeClass():void
    {
        static::$cuckooFilter = static::$factory->createCuckooFilter(
            Keys::CUCKOO_FILTER,
            static::getReBloomClientConfig()
        );
        static::$reBloomClient  = self::getReBloomClient();
    }

    public function testReserve(): void
    {
        $result = static::$cuckooFilter->reserve(50);
        $this->assertTrue($result);
    }

    public function testAdd(): void
    {
        $result = static::$cuckooFilter->add(1);
        $this->assertTrue($result);
        $exists = static::$cuckooFilter->exists(1);
        $this->assertTrue($exists);
    }

    public function testAddIfNotExist(): void
    {
        $result = static::$cuckooFilter->addIfNotExist(1);
        $this->assertFalse($result);
        $exists = static::$cuckooFilter->addIfNotExist(2);
        $this->assertTrue($exists);
    }

    public function testInserts(): void
    {
        $values = range(3, 5);
        $result = static::$cuckooFilter->insert($values);
        foreach ($result as $item) {
            $this->assertTrue($item);
        }

        $exists = static::$cuckooFilter->insertIfNotExist($values);
        foreach ($exists as $item) {
            $this->assertFalse($item);
        }
    }

    public function testInsertWithOptions(): void
    {
        $values = range(6, 60);
        $result = static::$cuckooFilter->insert($values, [OptionalParams::CAPACITY => 100]);
        foreach ($result as $item) {
            $this->assertTrue($item);
        }

        $exists = static::$cuckooFilter->insertIfNotExist($values);
        foreach ($exists as $item) {
            $this->assertFalse($item);
        }
    }

    public function testCount(): void
    {
        $values = [61, 62];
        foreach ($values as $value) {
            $result = static::$cuckooFilter->count($value);
            $this->assertEquals(0, $result);
        }

        static::$cuckooFilter->insert($values);

        foreach ($values as $value) {
            $result = static::$cuckooFilter->count($value);
            $this->assertEquals(1, $result);
        }
    }

    public function testDelete(): void
    {
        $values = [61, 62];

        foreach ($values as $value) {
            $result = static::$cuckooFilter->count($value);
            $this->assertEquals(1, $result);
        }

        foreach ($values as $value) {
            $result = static::$cuckooFilter->delete($value);
            $this->assertTrue($result);
        }

        foreach ($values as $value) {
            $result = static::$cuckooFilter->delete($value);
            $this->assertFalse($result);
        }
    }

    public function testLoadChunk(): void
    {
        $newBloomFilter = static::$factory->createCuckooFilter('cf-load-chunk', static::getReBloomClientConfig());

        list ($iterator, $data) = static::$cuckooFilter->scanDump(0);
        $result = $newBloomFilter->loadChunk($iterator, $data);

        $this->assertTrue($result);
    }

    public function testCopy(): void
    {
        $result = static::$cuckooFilter->copy('other-cuckoo-filter');
        $this->assertTrue($result);

        $otherCuckooFilter = static::$factory->createCuckooFilter(
            'other-cuckoo-filter',
            static::getReBloomClientConfig()
        );

        $values = range(1, 60);
        foreach ($values as $item) {
            $exists = $otherCuckooFilter->exists($item);
            $this->assertTrue($exists);
        }
    }

    public function testCopyExceptionBecauseNoSourceFilter(): void
    {
        $this->expectException(ResponseException::class);
        $newCuckooFilter = static::$factory->createCuckooFilter('new-cuckoo-filter', static::getReBloomClientConfig());
        $newCuckooFilter->copy('other-cuckoo-filter');
    }

    public function testInfo(): void
    {
        $result = static::$cuckooFilter->info();
        $this->assertArrayHasKey(Keys::SIZE, $result);
        $this->assertArrayHasKey(Keys::NUMBER_BUCKETS, $result);
        $this->assertArrayHasKey(Keys::EXPANSION_RATE, $result);
        $this->assertEquals(2, $result[Keys::NUMBER_FILTERS]);
        $this->assertEquals(60, $result[Keys::NUMBER_ITEMS_INSERTED]);
        $this->assertEquals(2, $result[Keys::NUMBER_ITEMS_DELETED]);
        $this->assertEquals(2, $result[Keys::BUCKET_SIZE]);
        $this->assertEquals(20, $result[Keys::MAX_ITERATIONS]);
    }
}
