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

interface RedisClientValidatorInterface
{
    /**
     * @param array $moduleListCommandResponse
     * @return bool
     */
    public function isRedisBloomModuleInstalled(array $moduleListCommandResponse): bool;
}
