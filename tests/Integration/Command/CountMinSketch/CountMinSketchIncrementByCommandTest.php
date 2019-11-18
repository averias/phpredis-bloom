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

class CountMinSketchIncrementByCommandTest extends BaseTestIntegration
{
    public function testIncrementSuccessfully(): void
    {
        static::$reBloomClient->countMinSketchInitByDim('key-incr1', 4, 4);
        $result = static::$reBloomClient->countMinSketchIncrementBy('key-incr1', 'green', 40, 'black', 90, 'orange', 6);
        $this->assertTrue($result);

        $query = static::$reBloomClient->countMinSketchQuery('key-incr1', 'green', 'black', 'orange');
        $this->assertEquals(40, $query[0]);
        $this->assertEquals(90, $query[1]);
        $this->assertEquals(6, $query[2]);

        $result = static::$reBloomClient->countMinSketchIncrementBy('key-incr1', 12, 31, 13.4, 32);
        $this->assertTrue($result);

        $query = static::$reBloomClient->countMinSketchQuery('key-incr1', 12, 13.4);
        $this->assertEquals(31, $query[0]);
        $this->assertEquals(32, $query[1]);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param array $arguments
     */
    public function testInitException($key, $arguments): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->countMinSketchInitByDim('key-incr2', 4, 4);
        static::$reBloomClient->countMinSketchIncrementBy($key, ...$arguments);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-incr2', [true, 44]],
            ['key-incr2', ['bar', true]],
            ['key-incr2', ['foo', 4, 'bar']],
            ['key-incr2', ['foo', 4.3]],
            ['key-incr2', ['baz', true]],
            ['key-incr3', ['baz', 10]] // non-existent key
        ];
    }
}
