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

class OptionalParams extends Enum
{
    /** Optional Params Names */
    const CAPACITY = 'CAPACITY';
    const ERROR = 'ERROR';
    const NOCREATE = 'NOCREATE';
    const ITEMS = 'ITEMS';

    /** Optional Params Groups */
    const OPTIONAL_PARAMS_BF_INSERT = [
        self::CAPACITY => null,
        self::ERROR => null,
        self::NOCREATE => null
    ];
}
