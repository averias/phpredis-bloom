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

namespace Averias\RedisBloom\Tests\Unit\DataTypes;

use Averias\RedisBloom\Adapter\RedisClientAdapter;
use Averias\RedisBloom\DataTypes\BloomFilter;
use Averias\RedisBloom\DataTypes\CuckooFilter;
use Averias\RedisBloom\Exception\ResponseException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Exception;

class DataTypesTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     * @param string $filterClassName
     */
    public function testCopyDoubleException(string $filterClassName): void
    {
        $this->expectException(ResponseException::class);
        $bloomFilterMock = $this->getMockBuilder($filterClassName)
            ->setConstructorArgs(['test-filter-name', $this->getRedisClientAdapterMock()])
            ->onlyMethods(['scanDump'])
            ->getMock();
        $bloomFilterMock->method('scanDump')
            ->willThrowException(new Exception());

        $bloomFilterMock->copy('test-filter-name');
    }

    protected function getRedisClientAdapterMock(): MockObject
    {
        $mock = $this->getMockBuilder(RedisClientAdapter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['executeCommandByName'])
            ->getMock();
        $mock
            ->expects($this->once())
            ->method('executeCommandByName')
            ->with('del', ['test-filter-name'])
            ->willThrowException(new Exception());

        return $mock;
    }

    public function getDataProvider(): array
    {
        return [
            [BloomFilter::class],
            [CuckooFilter::class]
        ];
    }
}
