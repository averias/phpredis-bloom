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

namespace Averias\RedisBloom\Tests\Integration\Factory;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Enum\Connection;
use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Factory\RedisBloomFactory;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class RedisBloomFactoryTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
    }

    public function testFactoryWithDifferentConfigurations(): void
    {
        $config = static::getReBloomClientConfig();
        $factory = new RedisBloomFactory(array_merge($config, [Connection::DATABASE => 15]));
        $clientDB15 = $factory->createClient();

        $bloomFilterDB14 = $factory->createBloomFilter(
            Keys::EXTENDED_KEY,
            array_merge($config, [Connection::DATABASE => 14])
        );

        $adapterDB13 = $factory->getAdapter(array_merge($config, [Connection::DATABASE => 13]));

        $clientDB15->bloomFilterAdd(Keys::EXTENDED_KEY, 'database15');
        $bloomFilterDB14->add('database14');
        $adapterDB13->executeBloomCommand(BloomCommands::BF_ADD, Keys::EXTENDED_KEY, ['database13']);

        $clientDB14 = $factory->createClient(array_merge($config, [Connection::DATABASE => 14]));
        $clientDB13 = $factory->createClient(array_merge($config, [Connection::DATABASE => 13]));

        $this->assertTrue($clientDB15->bloomFilterExists(Keys::EXTENDED_KEY, 'database15'));
        $this->assertFalse($clientDB15->bloomFilterExists(Keys::EXTENDED_KEY, 'database14'));
        $this->assertFalse($clientDB15->bloomFilterExists(Keys::EXTENDED_KEY, 'database13'));

        $this->assertFalse($clientDB14->bloomFilterExists(Keys::EXTENDED_KEY, 'database15'));
        $this->assertTrue($clientDB14->bloomFilterExists(Keys::EXTENDED_KEY, 'database14'));
        $this->assertFalse($clientDB14->bloomFilterExists(Keys::EXTENDED_KEY, 'database13'));

        $this->assertFalse($clientDB13->bloomFilterExists(Keys::EXTENDED_KEY, 'database15'));
        $this->assertFalse($clientDB13->bloomFilterExists(Keys::EXTENDED_KEY, 'database14'));
        $this->assertTrue($clientDB13->bloomFilterExists(Keys::EXTENDED_KEY, 'database13'));

        $clientDB15->executeRawCommand('DEL', Keys::EXTENDED_KEY);
        $clientDB14->del(Keys::EXTENDED_KEY);
        $clientDB13->executeRawCommand('DEL', Keys::EXTENDED_KEY);
    }
}
