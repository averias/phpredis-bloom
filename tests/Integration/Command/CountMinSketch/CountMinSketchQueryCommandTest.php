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

namespace Averias\RedisBloom\Tests\Integration\Command\CountMinSketch;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CountMinSketchQueryCommandTest extends BaseTestIntegration
{
    public function testSuccessfulQuery(): void
    {
        static::$reBloomClient->countMinSketchInitByDim('key-query1', 10, 10);
        static::$reBloomClient->countMinSketchIncrementBy('key-query1', 'blue', 33, 'yellow', 44, 'red', 16, 12, 14, 1.1, 1);

        $query1 = static::$reBloomClient->countMinSketchQuery('key-query1', 'blue', 'yellow', 'red', 12, 1.1);
        $this->assertEquals(33, $query1[0]);
        $this->assertEquals(44, $query1[1]);
        $this->assertEquals(16, $query1[2]);
        $this->assertEquals(14, $query1[3]);
        $this->assertEquals(1, $query1[4]);

        static::$reBloomClient->countMinSketchIncrementBy('key-query1', 'blue', 47, 'yellow', 1, 'black', 100);

        $query2 = static::$reBloomClient->countMinSketchQuery('key-query1', 'blue', 'yellow', 'red', 'black', 'orange');
        $this->assertEquals(80, $query2[0]);
        $this->assertEquals(45, $query2[1]);
        $this->assertEquals(16, $query2[2]);
        $this->assertEquals(100, $query2[3]);
        $this->assertEquals(0, $query2[4]);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $arguments
     */
    public function testQueryException($key, $arguments): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->countMinSketchInitByDim('key-query2', 4, 4);
        static::$reBloomClient->countMinSketchQuery($key, ...$arguments);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-query2', [true, 44]],
            ['key-query2', [['foo'], true]],
            ['key-query2', ['foo', 4, 'bar']] // non-existent key
        ];
    }
}
