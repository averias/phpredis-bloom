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

namespace Averias\RedisBloom\Tests\Integration\Command\TopK;

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\BaseTestIntegration;

class TopKInfoCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->topKReserve('key-info1', 2, 100, 3, 0.95);
        static::$reBloomClient->topKReserve('key-info2', 3, 200, 5, 0.91);
    }

    public function testSuccessfulInfo(): void
    {
        $info1 = static::$reBloomClient->topKInfo('key-info1');
        $this->assertEquals(2, $info1[Keys::K_SIZE]);
        $this->assertEquals(100, $info1[Keys::WIDTH]);
        $this->assertEquals(3, $info1[Keys::DEPTH]);
        $this->assertEquals(0.95, $info1[Keys::DECAY]);

        $info2 = static::$reBloomClient->topKInfo('key-info2');
        $this->assertEquals(3, $info2[Keys::K_SIZE]);
        $this->assertEquals(200, $info2[Keys::WIDTH]);
        $this->assertEquals(5, $info2[Keys::DEPTH]);
        $this->assertEquals(0.91, $info2[Keys::DECAY]);
    }

    public function testInfoException(): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->countMinSketchQuery('key-info3'); // top-k key doesn't exist
    }
}
