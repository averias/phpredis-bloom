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

class CountMinSketchInfoCommandTest extends BaseTestIntegration
{
    public function testSuccessfulInfo(): void
    {
        static::$reBloomClient->countMinSketchInitByDim('key-info1', 10, 10);
        static::$reBloomClient->countMinSketchIncrementBy('key-info1', 'blue', 10, 'yellow', 40);

        $query1 = static::$reBloomClient->countMinSketchInfo('key-info1');
        $this->assertEquals(10, $query1['width']);
        $this->assertEquals(10, $query1['depth']);
        $this->assertEquals(50, $query1['count']);

        static::$reBloomClient->countMinSketchIncrementBy('key-info1', 'blue', 86, 'yellow', 11);

        $query2 = static::$reBloomClient->countMinSketchInfo('key-info1');
        $this->assertEquals(10, $query2['width']);
        $this->assertEquals(10, $query2['depth']);
        $this->assertEquals(147, $query2['count']);
    }

    public function testInfoException(): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->countMinSketchQuery('key-info2');
    }
}
