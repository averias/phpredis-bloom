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

namespace Averias\RedisBloom\Validator;

use Averias\RedisBloom\Enum\Module;

class RedisClientValidator implements RedisClientValidatorInterface
{
    /**
     * @param array $moduleListCommandResponse
     * @return bool
     */
    public function isRedisBloomModuleInstalled(array $moduleListCommandResponse): bool
    {
        foreach ($moduleListCommandResponse as $group) {
            if (in_array(Module::REDIS_BLOOM_MODULE_NAME, $group)) {
                return true;
            }
        }

        return false;
    }
}
