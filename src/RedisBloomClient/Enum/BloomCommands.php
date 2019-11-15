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

namespace Averias\RedisBloom\Enum;

use MyCLabs\Enum\Enum;

class BloomCommands extends Enum
{
    /** Bloom Filter Commands */
    const BF_RESERVE = 'BF.RESERVE';
    const BF_ADD = 'BF.ADD';
    const BF_MADD = 'BF.MADD';
    const BF_INSERT = 'BF.INSERT';
    const BF_EXISTS = 'BF.EXISTS';
    const BF_MEXISTS = 'BF.MEXISTS';
    const BF_SCANDUMP = 'BF.SCANDUMP';
    const BF_LOADCHUNK = 'BF.LOADCHUNK';
}
