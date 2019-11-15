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

namespace Averias\RedisBloom\DataTypes;

use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Client\BaseRedisBloomClient;
use Averias\RedisBloom\Parser\ParserTrait;
use Averias\RedisBloom\Validator\InputValidatorTrait;

class BaseDataType extends BaseRedisBloomClient
{
    use InputValidatorTrait;
    use ParserTrait;

    /** @var string */
    protected $name;

    public function __construct(string $filterName, RedisClientAdapterInterface $redisClientAdapter)
    {
        parent::__construct($redisClientAdapter);
        $this->name = $filterName;
    }
}
