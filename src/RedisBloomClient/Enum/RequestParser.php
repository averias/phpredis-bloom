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

use Averias\RedisBloom\Parser\Request\BloomFilterInsertOptionalParams;
use MyCLabs\Enum\Enum;

class RequestParser extends Enum
{
    const COMMAND_PARSERS = [
        BloomCommands::BF_INSERT => BloomFilterInsertOptionalParams::class,
    ];
}
