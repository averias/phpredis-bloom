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
use Averias\RedisBloom\Connection\ConnectionOptions;
use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Exception\ConnectionException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use Redis;
use Exception;

class RedisAdapterTest extends TestCase
{
    /**
     * @dataProvider getConnectionExceptionDataProvider
     * @param string $connectMethodName
     * @param bool $isPersistentConnection
     */
    public function testConnectionExceptions(string $connectMethodName, bool $isPersistentConnection): void
    {
        $this->expectException(ConnectionException::class);

        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            $isPersistentConnection,
            0,
            $this->never(),
            $this->once(),
            $this->once()
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            $connectMethodName,
            false,
            0,
            $this->never(),
            false,
            $this->never(),
            $this->never()
        );

        new RedisAdapter($redisMock, $connectionOptionsMock);
    }

    /**
     * @dataProvider getSuccessConnectionDataProvider
     * @param string $connectMethodName
     * @param bool $isPersistentConnection
     * @param int $databaseIndex
     * @param InvokedCount $selectMethodExpectation
     * @param InvokedCount $getDatabaseMethodExpectation
     */
    public function testSuccessConnection(
        string $connectMethodName,
        bool $isPersistentConnection,
        int $databaseIndex,
        InvokedCount $selectMethodExpectation,
        InvokedCount $getDatabaseMethodExpectation
    ): void {
        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            $isPersistentConnection,
            $databaseIndex,
            $getDatabaseMethodExpectation,
            $this->once(),
            $this->once()
        );

        $connectionOptionsMock->expects($this->once())->method('getHost')->willReturn('127.0.0.1');
        $connectionOptionsMock->expects($this->once())->method('getPort')->willReturn(6379);

        $redisMock = $this->getRedisMockForSuccessConnection(
            $connectMethodName,
            true,
            $databaseIndex,
            $selectMethodExpectation,
            false,
            $this->never(),
            $this->once()
        );

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $this->assertSame('127.0.0.1', $adapter->getConnectionHost());
        $this->assertSame(6379, $adapter->getConnectionPort());
        $this->assertSame($databaseIndex, $adapter->getConnectionDatabase());
    }

    public function testExecuteCommandByName(): void
    {
        $methodName = 'hset';
        $arguments = ['hash-test', 'hash-field', 'hash-value'];

        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            false,
            0,
            $this->once(),
            $this->once(),
            $this->once()
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            'connect',
            true,
            0,
            $this->never(),
            true,
            $this->once(),
            $this->once()
        );

        $redisMock->expects($this->once())
            ->method($methodName)
            ->with(...$arguments)
            ->willReturn(true);

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $result = $adapter->executeCommandByName($methodName, $arguments);

        $this->assertTrue($result);
    }

    public function testExecuteCommandByNameException(): void
    {
        $this->expectException(Exception::class);

        $methodName = 'hset';
        $arguments = ['hash-test', 'hash-field', 'hash-value'];

        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            false,
            0,
            $this->once(),
            $this->once(),
            $this->once()
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            'connect',
            true,
            0,
            $this->never(),
            true,
            $this->once(),
            $this->once()
        );

        $redisMock->expects($this->once())
            ->method($methodName)
            ->with(...$arguments)
            ->willThrowException(new Exception());

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $adapter->executeCommandByName($methodName, $arguments);
    }

    public function testExecuteRawCommand(): void
    {
        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            false,
            0,
            $this->once(),
            $this->once(),
            $this->once()
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            'connect',
            true,
            0,
            $this->never(),
            true,
            $this->once(),
            $this->once()
        );

        $redisMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willReturn(true);

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $result = $adapter->rawCommand(BloomCommands::BF_ADD, 'key', 'foo');

        $this->assertTrue($result);
    }

    public function testExecuteRawCommandException(): void
    {
        $this->expectException(Exception::class);

        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            false,
            0,
            $this->once(),
            $this->once(),
            $this->once()
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            'connect',
            true,
            0,
            $this->never(),
            true,
            $this->once(),
            $this->once()
        );

        $redisMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willThrowException(new Exception());

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $adapter->rawCommand(BloomCommands::BF_ADD, 'key', 'foo');
    }

    public function testReconnectAfterCheckConnection(): void
    {
        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            false,
            0,
            $this->exactly(2),
            $this->exactly(2),
            $this->exactly(2)
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            'connect',
            true,
            0,
            $this->never(),
            false,
            $this->once(),
            $this->exactly(2)
        );

        $redisMock->expects($this->once())
            ->method('rawCommand')
            ->with(BloomCommands::BF_ADD, 'key', 'foo')
            ->willReturn(true);

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $result = $adapter->rawCommand(BloomCommands::BF_ADD, 'key', 'foo');

        $this->assertTrue($result);
    }

    public function testReconnectException(): void
    {
        $this->expectException(Exception::class);
        $connectionOptionsMock = $this->getConnectionOptionsMockForSuccessConnection(
            false,
            0,
            $this->once(),
            $this->exactly(2),
            $this->exactly(2)
        );

        $redisMock = $this->getRedisMockForSuccessConnection(
            'connect',
            null,
            0,
            $this->never(),
            false,
            $this->once(),
            $this->once()
        );
        $redisMock->expects($this->any())
            ->method('connect')
            ->with('localhost')
            ->will($this->onConsecutiveCalls(true, false));

        $adapter = new RedisAdapter($redisMock, $connectionOptionsMock);
        $result = $adapter->rawCommand(BloomCommands::BF_ADD, 'key', 'foo');

        $this->assertTrue($result);
    }

    /**
     * @return MockObject|ConnectionOptions
     */
    protected function getConnectionOptionsMock(): MockObject
    {
        return $this->getMockBuilder(ConnectionOptions::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConnectionValues', 'getDatabase', 'isPersistent', 'getHost', 'getPort'])
            ->getMock();
    }

    /**
     * @param bool $isPersistentConnection
     * @param int $databaseIndex
     * @param InvokedCount $getDatabaseMethodExpectation
     * @param InvokedCount $getConnectionValuesMethodExpectation
     * @param InvokedCount $isPersistentMethodExpectation
     * @return MockObject|ConnectionOptions
     */
    protected function getConnectionOptionsMockForSuccessConnection(
        bool $isPersistentConnection,
        int $databaseIndex,
        InvokedCount $getDatabaseMethodExpectation,
        InvokedCount $getConnectionValuesMethodExpectation,
        InvokedCount $isPersistentMethodExpectation
    ): MockObject {
        $connectionValues = ['localhost'];
        $connectionOptionsMock = $this->getConnectionOptionsMock();
        $connectionOptionsMock->expects($isPersistentMethodExpectation)
            ->method('isPersistent')
            ->willReturn($isPersistentConnection);
        $connectionOptionsMock->expects($getConnectionValuesMethodExpectation)
            ->method('getConnectionValues')
            ->willReturn($connectionValues);
        $connectionOptionsMock->expects($getDatabaseMethodExpectation)
            ->method('getDatabase')
            ->willReturn($databaseIndex);

        return $connectionOptionsMock;
    }

    /**
     * @return MockObject|Redis
     */
    protected function getRedisMock(): MockObject
    {
        return $this->getMockBuilder(Redis::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getLastError',
                    'rawCommand',
                    'select',
                    'setOption',
                    'pconnect',
                    'connect',
                    'isConnected',
                    'close',
                    'hset'
                ]
            )
            ->getMock();
    }

    /**
     * @param string $connectMethodName
     * @param bool|null $returnValueForConnectMethod
     * @param int $databaseIndex
     * @param InvokedCount $selectMethodExpectation
     * @param bool $isConnected
     * @param InvokedCount|null $isConnectedMethodExpectation
     * @param InvokedCount $setOptionMethodExpectation
     * @return MockObject|Redis
     */
    protected function getRedisMockForSuccessConnection(
        string $connectMethodName,
        ?bool $returnValueForConnectMethod,
        int $databaseIndex,
        InvokedCount $selectMethodExpectation,
        bool $isConnected,
        ?InvokedCount $isConnectedMethodExpectation,
        InvokedCount $setOptionMethodExpectation
    ): MockObject {
        $connectionValues = ['localhost'];
        $redisMock = $this->getRedisMock();
        if (!is_null($returnValueForConnectMethod)) {
            $redisMock->expects($this->any())
                ->method($connectMethodName)
                ->with(...$connectionValues)
                ->willReturn($returnValueForConnectMethod);
        }
        $redisMock->expects($setOptionMethodExpectation)
            ->method('setOption')
            ->with(Redis::OPT_REPLY_LITERAL, true);
        $redisMock->expects($selectMethodExpectation)
            ->method('select')
            ->with($databaseIndex);
        $redisMock->expects($isConnectedMethodExpectation)
            ->method('isConnected')
            ->willReturn($isConnected);

        return $redisMock;
    }

    public function getSuccessConnectionDataProvider(): array
    {
        return [
            ['pconnect', true, 1, $this->once(), $this->exactly(3)],
            ['connect', false, 1, $this->once(), $this->exactly(3)],
            ['pconnect', true, 0, $this->never(), $this->exactly(2)],
            ['connect', false, 0, $this->never(), $this->exactly(2)]
        ];
    }

    public function getConnectionExceptionDataProvider(): array
    {
        return [
            ['pconnect', true],
            ['connect', false]
        ];
    }
}
