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

use Averias\RedisBloom\DataTypes\CountMinSketch;
use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CountMinSketchTest extends BaseTestIntegration
{
    /** @var CountMinSketch */
    protected static $countMinSketch;

    /**
     * @throws RedisClientException
     */
    public static function setUpBeforeClass():void
    {
        static::$countMinSketch = static::$factory->createCountMinSketch(
            Keys::COUNT_MIN_SKETCH,
            static::getReBloomClientConfig()
        );
        static::$reBloomClient  = self::getReBloomClient();
    }

    public function testInit(): void
    {
        $result = static::$countMinSketch->initByDim(100, 10);
        $this->assertTrue($result);

        $info = static::$countMinSketch->info();
        $this->assertEquals(100, $info[Keys::WIDTH]);
        $this->assertEquals(10, $info[Keys::DEPTH]);
        $this->assertEquals(0, $info[Keys::COUNT]);

        $anotherCountMinSketch = static::$factory->createCountMinSketch('cms-key', static::getReBloomClientConfig());
        $result = $anotherCountMinSketch->initByProb(0.01, 0.1);
        $this->assertTrue($result);
    }

    public function testIncrement(): void
    {
        $result = static::$countMinSketch->incrementBy('green', 321, 'red', 123);

        $this->assertEquals(321, $result[0]);
        $this->assertEquals(123, $result[1]);
    }

    public function testMerge(): void
    {
        $cms1 = static::$factory->createCountMinSketch('cms1-key', static::getReBloomClientConfig());
        $cms1->initByDim(100, 10);
        $cms1->incrementBy('purple', 111, 'yellow', 222);

        $mergedCms = static::$factory->createCountMinSketch('merged-key', static::getReBloomClientConfig());
        $mergedCms->initByDim(100, 10);
        $mergedCms->mergeFrom(2, [Keys::COUNT_MIN_SKETCH, 'cms1-key'], [10, 100]);

        $query = $mergedCms->query('green', 'red', 'purple', 'yellow');
        $this->assertEquals(3210, $query[0]);
        $this->assertEquals(1230, $query[1]);
        $this->assertEquals(11100, $query[2]);
        $this->assertEquals(22200, $query[3]);
    }
}
