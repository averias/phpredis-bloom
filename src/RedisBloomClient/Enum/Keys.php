<?php
/**
 * @project   phpredis-bloom
 * @author    Rafael Campoy <rafa.campoy@gmail.com>
 * @copyright 2019 Rafael Campoy <rafa.campoy@gmail.com>
 * @license   MIT
 * @link      https://github.com/averias/php-rejson
 *
 * Copyright and license information, is included in
 * the LICENSE file that is distributed with this source code.
 */
namespace Averias\RedisBloom\Enum;

use MyCLabs\Enum\Enum;

class Keys extends Enum
{
    const DEFAULT_KEY = 'test-key';
    const EXTENDED_KEY = 'extended-test-key';
    const KEY_TO_EMPTY = 'key-to-empty';
    const BLOOM_FILTER = 'boom-filter-key';
}
