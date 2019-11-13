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

namespace Averias\RedisBloom\Tests\Integration\DataTypes;

use Averias\RedisBloom\DataTypes\BloomFilter;
use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Enum\OptionalParams;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class BloomFilterTest extends BaseTestIntegration
{
    const DATA_TYPE_NAME = 'boom-filter-key';

    /** @var BloomFilter */
    protected static $bloomFilter;

    /**
     * @throws RedisClientException
     */
    public static function setUpBeforeClass():void
    {
        static::$bloomFilter = static::$factory->createBloomFilter(Keys::BLOOM_FILTER, static::getReBloomClientConfig());
        static::$reBloomClient  = self::getReBloomClient();
    }

    public function testReserve()
    {
        $result = static::$bloomFilter->reserve(0.1, 50);
        $this->assertTrue($result);
    }

    public function testAdd()
    {
        $result = static::$bloomFilter->add(1);
        $this->assertTrue($result);
        $exists = static::$bloomFilter->exists(1);
        $this->assertTrue($exists);
    }

    public function testMultiAdd()
    {
        $values = range(2, 5);
        $result = static::$bloomFilter->multiAdd(...$values);
        foreach ($result as $item) {
            $this->assertTrue($item);
        }

        $exists = static::$bloomFilter->multiExists(...$values);
        foreach ($exists as $item) {
            $this->assertTrue($item);
        }
    }

    public function testInsertWithOptions()
    {
        $values = range(6, 20);
        $result = static::$bloomFilter->insert($values, [OptionalParams::CAPACITY => 100, OptionalParams::ERROR => 0.01]);
        foreach ($result as $item) {
            $this->assertTrue($item);
        }

        $exists = static::$bloomFilter->multiExists(...$values);
        foreach ($exists as $item) {
            $this->assertTrue($item);
        }
    }

    public function testCopy()
    {
        $result = static::$bloomFilter->copy('other-bloom-filter');
        $this->assertTrue($result);

        $otherBloomFilter = static::$factory->createBloomFilter(Keys::BLOOM_FILTER, static::getReBloomClientConfig());

        $values = range(1, 20);
        $exists = $otherBloomFilter->multiExists(...$values);
        foreach ($exists as $item) {
            $this->assertTrue($item);
        }
    }

    public function testCopyExceptionBecauseNoSourceFilter()
    {
        $this->expectException(ResponseException::class);
        $newBloomFilter = static::$factory->createBloomFilter('new-bloom-filter', static::getReBloomClientConfig());
        $newBloomFilter->copy('other-bloom-filter');
    }
}
