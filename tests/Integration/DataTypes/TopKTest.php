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

use Averias\RedisBloom\DataTypes\TopK;
use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class TopKTest extends BaseTestIntegration
{
    /** @var TopK */
    protected static $topK;

    /**
     * @throws RedisClientException
     */
    public static function setUpBeforeClass():void
    {
        static::$topK = static::$factory->createTopK(Keys::TOP_K, static::getReBloomClientConfig());
    }

    public function testReserve(): void
    {
        $result = static::$topK->reserve(2, 20, 3, 0.999);
        $this->assertTrue($result);
    }

    public function testAdd(): void
    {
        $result = static::$topK->add(12, 'foo', 'bar');
        $this->assertEquals($result, [false, false, false]);

        $result = static::$topK->add(12, 'baz', 'bar');
        $this->assertEquals($result, [false, false, 'foo']);
    }

    public function testIncrementBy(): void
    {
        $result = static::$topK->incrementBy(12, 10, 'foo', 15, 'bar', 1);
        $this->assertEquals($result, [false, 'bar', false]);

        $result = static::$topK->incrementBy(12, 1, 'baz', 27, 'bar', 123);
        $this->assertEquals($result, [false, '12', 'foo']);
    }

    public function testQuery(): void
    {
        $result = static::$topK->query(12, 'foo', 'bar', 'baz');
        $this->assertEquals($result, [false, false, true, true]);
    }

    public function testCount(): void
    {
        $result = static::$topK->count(12, 'foo', 'bar', 'baz');
        $this->assertEquals($result, [13, 16, 126, 28]);
    }

    public function testList(): void
    {
        $result = static::$topK->list();
        $this->assertEquals($result, ['baz', 'bar']);
    }

    public function testInfo(): void
    {
        $result = static::$topK->info();
        $this->assertEquals(
            $result,
            [
                'k' => 2,
                'width' => 20,
                'depth' => 3,
                'decay' => 0.999,
            ]
        );
    }
}
