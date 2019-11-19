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

    /** Cuckoo Filter Commands */
    const CF_RESERVE = 'CF.RESERVE';
    const CF_ADD = 'CF.ADD';
    const CF_ADDNX = 'CF.ADDNX';
    const CF_INSERT = 'CF.INSERT';
    const CF_INSERTNX = 'CF.INSERTNX';
    const CF_EXISTS = 'CF.EXISTS';
    const CF_DEL = 'CF.DEL';
    const CF_COUNT = 'CF.COUNT';
    const CF_SCANDUMP = 'CF.SCANDUMP';
    const CF_LOADCHUNK = 'CF.LOADCHUNK';

    /** Count-Min Sketch Commands */
    const CMS_INITBYDIM = 'CMS.INITBYDIM';
    const CMS_INITBYPROB = 'CMS.INITBYPROB';
    const CMS_INCRBY = 'CMS.INCRBY';
    const CMS_QUERY = 'CMS.QUERY';
    const CMS_MERGE = 'CMS.MERGE';
    const CMS_INFO = 'CMS.INFO';

    /** Top-K Commands */
    const TOPK_RESERVE = 'TOPK.RESERVE';
    const TOPK_ADD = 'TOPK.ADD';
    const TOPK_INCRBY = 'TOPK.INCRBY';
    const TOPK_QUERY = 'TOPK.QUERY';
    const TOPK_COUNT = 'TOPK.COUNT';
    const TOPK_LIST = 'TOPK.LIST';
    const TOPK_INFO = 'TOPK.INFO';
}
