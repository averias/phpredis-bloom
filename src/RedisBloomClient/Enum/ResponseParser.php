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

use Averias\RedisBloom\Parser\Response\ArrayFlatToAssociative;
use Averias\RedisBloom\Parser\Response\ArrayOfIntegerToBool;
use Averias\RedisBloom\Parser\Response\IntegerToBool;
use Averias\RedisBloom\Parser\Response\OkToTrue;
use MyCLabs\Enum\Enum;

class ResponseParser extends Enum
{
    const COMMAND_PARSERS = [
        BloomCommands::BF_RESERVE => OkToTrue::class,
        BloomCommands::BF_ADD => IntegerToBool::class,
        BloomCommands::BF_MADD => ArrayOfIntegerToBool::class,
        BloomCommands::BF_INSERT => ArrayOfIntegerToBool::class,
        BloomCommands::BF_EXISTS => IntegerToBool::class,
        BloomCommands::BF_MEXISTS => ArrayOfIntegerToBool::class,
        BloomCommands::BF_LOADCHUNK => OkToTrue::class,
        BloomCommands::BF_INFO => ArrayFlatToAssociative::class,
        BloomCommands::CF_RESERVE => OkToTrue::class,
        BloomCommands::CF_ADD => IntegerToBool::class,
        BloomCommands::CF_ADDNX => IntegerToBool::class,
        BloomCommands::CF_INSERT => ArrayOfIntegerToBool::class,
        BloomCommands::CF_INSERTNX => ArrayOfIntegerToBool::class,
        BloomCommands::CF_EXISTS => IntegerToBool::class,
        BloomCommands::CF_DEL => IntegerToBool::class,
        BloomCommands::CF_LOADCHUNK => OkToTrue::class,
        BloomCommands::CF_INFO => ArrayFlatToAssociative::class,
        BloomCommands::CMS_INITBYDIM => OkToTrue::class,
        BloomCommands::CMS_INITBYPROB => OkToTrue::class,
        BloomCommands::CMS_MERGE => OkToTrue::class,
        BloomCommands::CMS_INFO => ArrayFlatToAssociative::class,
        BloomCommands::TOPK_RESERVE => OkToTrue::class,
        BloomCommands::TOPK_QUERY => ArrayOfIntegerToBool::class,
        BloomCommands::TOPK_INFO => ArrayFlatToAssociative::class
    ];
}
