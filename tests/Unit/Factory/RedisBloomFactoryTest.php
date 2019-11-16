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

namespace Averias\RedisBloom\Tests\Unit\Factory;

use Averias\RedisBloom\Adapter\AdapterProvider;
use Averias\RedisBloom\Adapter\RedisClientAdapter;
use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\DataTypes\BloomFilter;
use Averias\RedisBloom\DataTypes\CuckooFilter;
use Averias\RedisBloom\DataTypes\DataTypeInterface;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Factory\RedisBloomFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RedisBloomFactoryTest extends TestCase
{
    public function testCreateClientException(): void
    {
        $this->expectException(RedisClientException::class);
        $factory = new RedisBloomFactory();
        $factory->createClient([
            'database' => 62
        ]);
    }

    public function testFactoryCreations()
    {
        $adapterProviderMock = $this->getAdapterProviderMock();
        $adapterProviderMock
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->getRedisClientAdapterMock());

        $factoryMock = $this->getRedisBloomFactoryMock();
        $factoryMock
            ->expects($this->any())
            ->method('getAdapterProvider')
            ->willReturn($adapterProviderMock);
        $client = $factoryMock->createClient();

        $this->assertInstanceOf(RedisBloomClientInterface::class, $client);

        $client = $factoryMock->createBloomFilter('bloom-filter-test');

        $this->assertInstanceOf(DataTypeInterface::class, $client);
        $this->assertInstanceOf(BloomFilter::class, $client);

        $client = $factoryMock->createCuckooFilter('cuckoo-filter-test');

        $this->assertInstanceOf(DataTypeInterface::class, $client);
        $this->assertInstanceOf(CuckooFilter::class, $client);
    }

    protected function getRedisBloomFactoryMock(): MockObject
    {
        return $this->getMockBuilder(RedisBloomFactory::class)
            ->onlyMethods(['getAdapterProvider'])
            ->getMock();
    }

    protected function getAdapterProviderMock(): MockObject
    {
        return $this->getMockBuilder(AdapterProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();
    }

    /**
     * @return MockObject|RedisClientAdapterInterface
     */
    protected function getRedisClientAdapterMock(): MockObject
    {
        return $this->getMockBuilder(RedisClientAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
