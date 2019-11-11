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

use Averias\RedisBloom\Parser\Response\ArrayOfIntegerToBool;
use Averias\RedisBloom\Parser\Response\IntegerToBool;
use Averias\RedisBloom\Parser\Response\OkToTrue;
use MyCLabs\Enum\Enum;

class ResponseParser extends Enum
{
    const RESPONSE_PARSER = [
        BloomCommands::BF_RESERVE => OkToTrue::class,
        BloomCommands::BF_ADD => IntegerToBool::class,
        BloomCommands::BF_MADD => ArrayOfIntegerToBool::class,
        BloomCommands::BF_INSERT => ArrayOfIntegerToBool::class,
        BloomCommands::BF_EXISTS => IntegerToBool::class,
        BloomCommands::BF_MEXISTS => ArrayOfIntegerToBool::class,
    ];
}
