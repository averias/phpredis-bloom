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

namespace Averias\RedisBloom\Tests\Unit\Validator;

use Averias\RedisBloom\Enum\Module;
use Averias\RedisBloom\Validator\RedisClientValidator;
use PHPUnit\Framework\TestCase;

class RedisClientValidatorTest extends TestCase
{
    public function testIsRedisBloomModuleInstalled(): void
    {
        $validator = new RedisClientValidator();
        $result = $validator->isRedisBloomModuleInstalled([['foo', 'bar'], [Module::REDIS_BLOOM_MODULE_NAME]]);
        $this->assertTrue($result);
    }

    public function testIsNotRedisBloomModuleInstalled(): void
    {
        $validator = new RedisClientValidator();
        $result = $validator->isRedisBloomModuleInstalled([['foo', 'bar'], ['bfNNotInstalled']]);
        $this->assertFalse($result);
    }
}
