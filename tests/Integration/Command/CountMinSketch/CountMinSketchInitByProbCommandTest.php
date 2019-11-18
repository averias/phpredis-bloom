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
use TypeError;

class CountMinSketchInitByProbCommandTest extends BaseTestIntegration
{
    public function testInitSuccessfully(): void
    {
        $result = static::$reBloomClient->countMinSketchInitByProb('key-init1', 0.001, 0.3);
        $this->assertTrue($result);
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
        static::$reBloomClient->countMinSketchInitByProb($key, $width, $depth);
    }

    public function getExceptionDataProvider(): array
    {
        return [
            ['key-init2', 'foo', 44, TypeError::class], // 'foo' is not float
            ['key-init2', 4, 'bar', TypeError::class], // 'bar' is not float
            ['key-init2', 4, 0.4, ResponseException::class], // 'foo' is not float
            ['key-init2', 0.4, 4, ResponseException::class], // 'foo' is not float
            ['key-init2', 0.0, 0.4, ResponseException::class],  // 0.0 is not a valid value
            ['key-init2', 0.1, 1.0, ResponseException::class],  // 1.0 is not a valid value
            ['key-init1', 0.4, 0.4, ResponseException::class]  // existent key
        ];
    }
}
