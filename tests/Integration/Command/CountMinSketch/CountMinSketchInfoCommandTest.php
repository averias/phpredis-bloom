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

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class CountMinSketchInfoCommandTest extends BaseTestIntegration
{
    public function testSuccessfulInfo(): void
    {
        static::$reBloomClient->countMinSketchInitByDim('key-info1', 10, 10);
        static::$reBloomClient->countMinSketchIncrementBy('key-info1', 'blue', 10, 'yellow', 40);

        $info1 = static::$reBloomClient->countMinSketchInfo('key-info1');
        $this->assertEquals(10, $info1[Keys::WIDTH]);
        $this->assertEquals(10, $info1[Keys::DEPTH]);
        $this->assertEquals(50, $info1[Keys::COUNT]);

        static::$reBloomClient->countMinSketchIncrementBy('key-info1', 'blue', 86, 'yellow', 11);

        $info2 = static::$reBloomClient->countMinSketchInfo('key-info1');
        $this->assertEquals(10, $info2[Keys::WIDTH]);
        $this->assertEquals(10, $info2[Keys::DEPTH]);
        $this->assertEquals(147, $info2[Keys::COUNT]);
    }

    public function testInfoException(): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->countMinSketchQuery('key-info2');
    }
}
