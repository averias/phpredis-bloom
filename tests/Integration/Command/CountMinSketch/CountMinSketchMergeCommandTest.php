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

namespace Averias\RedisBloom\Tests\Integration\Command\CountMinSketch;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class CountMinSketchMergeCommandTest extends BaseTestIntegration
{
    public function testSuccessfulMerge(): void
    {
        static::$reBloomClient->countMinSketchInitByDim('key-merge1', 10, 10);
        static::$reBloomClient->countMinSketchInitByDim('key-merge2', 10, 10);
        static::$reBloomClient->countMinSketchInitByDim('key-merge3', 10, 10);
        static::$reBloomClient->countMinSketchInitByDim('key-merge4', 10, 10);
        static::$reBloomClient->countMinSketchInitByDim('key-merge5', 10, 10);
        static::$reBloomClient->countMinSketchInitByDim('key-merge7', 30, 30);

        static::$reBloomClient->countMinSketchIncrementBy('key-merge1', 'blue', 77);
        static::$reBloomClient->countMinSketchIncrementBy('key-merge2', 'yellow', 88, 'red', 61);
        static::$reBloomClient->countMinSketchIncrementBy('key-merge3', 12, 4, 1.1, 100);

        $result = static::$reBloomClient->countMinSketchMerge(
            'key-merge4',
            3,
            ['key-merge1', 'key-merge2', 'key-merge3']
        );
        $this->assertTrue($result);

        $query1 = static::$reBloomClient->countMinSketchQuery('key-merge4', 'blue', 'yellow', 'red', 12, 1.1);
        $this->assertEquals(77, $query1[0]);
        $this->assertEquals(88, $query1[1]);
        $this->assertEquals(61, $query1[2]);
        $this->assertEquals(4, $query1[3]);
        $this->assertEquals(100, $query1[4]);

        $result = static::$reBloomClient->countMinSketchMerge(
            'key-merge5',
            3,
            ['key-merge1', 'key-merge2', 'key-merge3'],
            [10, 100, 1000]
        );
        $this->assertTrue($result);

        $query1 = static::$reBloomClient->countMinSketchQuery('key-merge5', 'blue', 'yellow', 'red', 12, 1.1);
        $this->assertEquals(770, $query1[0]);
        $this->assertEquals(8800, $query1[1]);
        $this->assertEquals(6100, $query1[2]);
        $this->assertEquals(4000, $query1[3]);
        $this->assertEquals(100000, $query1[4]);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param $numKeys
     * @param $sketchKeys
     * @param $weights
     */
    public function testMergeException($key, $numKeys, $sketchKeys, $weights): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->countMinSketchMerge($key, $numKeys, $sketchKeys, $weights);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-merge8', 2, ['key-merge1', 'key-merge2'], []], // not initialized key
            ['key-merge7', 1, ['key-merge1', 'key-merge2'], []], // wrong num keys params
            ['key-merge7', 2, ['key-merge1', 'key-merge2'], [2, 3, 4]], // more weights than keys
            ['key-merge7', 2, ['key-merge1', 'key-merge2'], [2, 3]] // different width and depth initialization
        ];
    }
}
