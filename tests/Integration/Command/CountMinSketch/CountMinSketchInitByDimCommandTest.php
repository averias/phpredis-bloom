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
use TypeError;

class CountMinSketchInitByDimCommandTest extends BaseTestIntegration
{
    public function testInitSuccessfully(): void
    {
        $result = static::$reBloomClient->countMinSketchInitByDim('key-init1', 4000, 4000);
        $this->assertTrue($result);

        $info = static::$reBloomClient->countMinSketchInfo('key-init1');
        $this->assertEquals(4000, $info[Keys::WIDTH]);
        $this->assertEquals(4000, $info[Keys::DEPTH]);
        $this->assertEquals(0, $info[Keys::COUNT]);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param string $key
     * @param int $width
     * @param int $depth
     * @param $exceptionClassName
     */
    public function testInitException($key, $width, $depth, $exceptionClassName): void
    {
        $this->expectException($exceptionClassName);
        static::$reBloomClient->countMinSketchInitByDim($key, $width, $depth);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-init2', 'foo', 44, TypeError::class],
            ['key-init2', 4, 'bar', TypeError::class],
            ['key-init1', 4, 4, ResponseException::class] // existent key
        ];
    }
}
