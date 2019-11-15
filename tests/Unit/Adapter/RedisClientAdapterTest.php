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

use Averias\RedisBloom\Adapter\RedisAdapter;
use Averias\RedisBloom\Adapter\RedisClientAdapter;
use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Exception\ResponseException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Exception;

class RedisClientAdapterTest extends TestCase
{
    public function testExecuteCommandByName(): void
    {
        $methodName = 'hset';
        $arguments = ['hash-test', 'hash-field', 'hash-value'];

        /** @var MockObject|RedisAdapter $redisAdapterMock */
        $redisAdapterMock = $this->getRedisAdapterMock();

        $redisAdapterMock
            ->expects($this->once())
            ->method('executeCommandByName')
            ->with($methodName, $arguments)
            ->willReturn(true);

        $adapter = new RedisClientAdapter($redisAdapterMock);
        $result = $adapter->executeCommandByName($methodName, $arguments);

        $this->assertTrue($result);
    }

    public function testExecuteCommandByNameException(): void
    {
        $this->expectException(ResponseException::class);

        $methodName = 'hset';
        $arguments = ['hash-test', 'hash-field', 'hash-value'];

        /** @var MockObject|RedisAdapter $redisAdapterMock */
        $redisAdapterMock = $this->getRedisAdapterMock();

        $redisAdapterMock
            ->expects($this->once())
            ->method('executeCommandByName')
            ->with($methodName, $arguments)
            ->willThrowException(new Exception());

        $adapter = new RedisClientAdapter($redisAdapterMock);
        $adapter->executeCommandByName($methodName, $arguments);
    }

    public function testExecuteRawCommand(): void
    {
        /** @var MockObject|RedisAdapter $redisAdapterMock */
        $redisAdapterMock = $this->getRedisAdapterMock();

        $redisAdapterMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willReturn(true);

        $adapter = new RedisClientAdapter($redisAdapterMock);
        $result = $adapter->executeRawCommand(BloomCommands::BF_ADD, 'key', 'foo');

        $this->assertTrue($result);
    }

    public function testExecuteRawCommandException(): void
    {
        $this->expectException(ResponseException::class);

        /** @var MockObject|RedisAdapter $redisAdapterMock */
        $redisAdapterMock = $this->getRedisAdapterMock();

        $redisAdapterMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willThrowException(new ResponseException());

        $adapter = new RedisClientAdapter($redisAdapterMock);
        $adapter->executeRawCommand(BloomCommands::BF_ADD, 'key', 'foo');
    }

    public function testExecuteBloomCommand(): void
    {
        /** @var MockObject|RedisAdapter $redisAdapterMock */
        $redisAdapterMock = $this->getRedisAdapterMock();

        $redisAdapterMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willReturn(true);

        $adapter = new RedisClientAdapter($redisAdapterMock);
        $result = $adapter->executeBloomCommand(BloomCommands::BF_ADD, 'key', ['foo']);

        $this->assertTrue($result);
    }

    public function testExecuteBloomCommandException(): void
    {
        $this->expectException(ResponseException::class);

        /** @var MockObject|RedisAdapter $redisAdapterMock */
        $redisAdapterMock = $this->getRedisAdapterMock();

        $redisAdapterMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willThrowException(new Exception());

        $adapter = new RedisClientAdapter($redisAdapterMock);
        $adapter->executeBloomCommand(BloomCommands::BF_ADD, 'key', ['foo']);
    }

    protected function getRedisAdapterMock(): MockObject
    {
        return $this->getMockBuilder(RedisAdapter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLastError', 'rawCommand', 'executeCommandByName'])
            ->getMock();
    }
}
