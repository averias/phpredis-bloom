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

namespace Averias\RedisBloom\Tests;

use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\Enum\Connection;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Factory\RedisBloomFactory;
use Averias\RedisBloom\Factory\RedisBloomFactoryInterface;
use PHPUnit\Framework\TestCase;

class BaseTestIntegration extends TestCase
{
    /** @var RedisBloomClientInterface */
    protected static $reBloomClient;

    /** @var RedisBloomFactoryInterface */
    protected static $factory;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        static::$factory = new RedisBloomFactory();
    }

    /**
     * @throws RedisClientException
     */
    public static function setUpBeforeClass():void
    {
        static::$reBloomClient  = self::getReBloomClient();
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$reBloomClient) {
            static::$reBloomClient->select(REDIS_TEST_DATABASE);
            static::$reBloomClient->flushDb();
        }
    }

    /**
     * @return array
     */
    protected static function getReBloomClientConfig(): array
    {
        return [
            Connection::HOST => REDIS_TEST_SERVER,
            Connection::PORT => (int) REDIS_TEST_PORT,
            Connection::TIMEOUT => 2,
            Connection::DATABASE => (int) REDIS_TEST_DATABASE
        ];
    }

    /**
     * @return RedisBloomClientInterface
     * @throws RedisClientException
     */
    protected static function getReBloomClient(): RedisBloomClientInterface
    {
        return static::$factory->createClient(static::getReBloomClientConfig());
    }
}
