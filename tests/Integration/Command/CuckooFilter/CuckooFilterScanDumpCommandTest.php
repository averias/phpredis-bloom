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

namespace Averias\RedisBloom\Tests\Integration\Command\CuckooFilter;

use Averias\RedisBloom\Enum\Keys;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Tests\Integration\BaseTestIntegration;

class CuckooFilterScanDumpCommandTest extends BaseTestIntegration
{
    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        static::$reBloomClient->cuckooFilterInsert(Keys::DEFAULT_KEY, ['foo', 12, 9, 1337, 'bar', 'baz']);
    }

    public function testScanDump(): void
    {
        $iterator = 0;
        while (true) {
            list ($iterator, $data) = $result = static::$reBloomClient->cuckooFilterScanDump(
                Keys::DEFAULT_KEY,
                $iterator
            );
            if ($iterator == 0) {
                break;
            }
            $this->assertNotNull($iterator);
            $this->assertNotEquals(0, $iterator);
            $this->assertNotEmpty($data);
        }
    }

    public function testNonExistentKeyException(): void
    {
        $this->expectException(ResponseException::class);
        static::$reBloomClient->cuckooFilterScanDump('nonexistent', 0);
    }
}
