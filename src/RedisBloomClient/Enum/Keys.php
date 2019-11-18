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

class Keys extends Enum
{
    const DEFAULT_KEY = 'test-key';
    const EXTENDED_KEY = 'extended-test-key';
    const BLOOM_FILTER = 'boom-filter-key';
    const CUCKOO_FILTER = 'cuckoo-filter-key';
    const COUNT_MIN_SKETCH = 'count-min-sketch-key';
    const TOP_K = 'top-k-key';
}
