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

namespace Averias\RedisBloom\Tests\Unit\Adapter;

use Averias\RedisBloom\Adapter\AdapterProvider;
use Averias\RedisBloom\Adapter\RedisClientAdapter;
use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Enum\Module;
use Averias\RedisBloom\Exception\RedisBloomModuleNotInstalledException;
use Averias\RedisBloom\Validator\RedisClientValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdapterProviderTest extends TestCase
{
    public function testRedisClientConfigurationException(): void
    {
        $this->expectException(RedisBloomModuleNotInstalledException::class);
        $this->expectExceptionMessage('RedisBloom module not installed in Redis server.');

        $providerMock = $this->getMockBuilder(AdapterProvider::class)
            ->setConstructorArgs([new RedisClientValidator()])
            ->onlyMethods(['getRedisClientAdapter'])
            ->getMock();
        $providerMock->method('getRedisClientAdapter')
            ->willReturn($this->getRedisClientMock([['foo', 'bar'], ['zoo', 'bad']]));
        $providerMock->get();
    }

    public function testGetRedisClientAdapterInterface(): void
    {
        $providerMock = $this->getMockBuilder(AdapterProvider::class)
            ->setConstructorArgs([new RedisClientValidator()])
            ->onlyMethods(['getRedisClientAdapter'])
            ->getMock();
        $providerMock->method('getRedisClientAdapter')
            ->willReturn(
                $this->getRedisClientMock([[Module::REDIS_BLOOM_MODULE_NAME]])
            );
        $adapter = $providerMock->get();
        $this->assertInstanceOf(RedisClientAdapterInterface::class, $adapter);
    }

    protected function getRedisClientMock(array $moduleList): MockObject
    {
        $redisClientMock =  $this->getMockBuilder(RedisClientAdapter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['executeRawCommand'])
            ->getMock();

        $redisClientMock->method('executeRawCommand')
            ->with('MODULE', 'list')
            ->willReturn($moduleList);

        return $redisClientMock;
    }
}
