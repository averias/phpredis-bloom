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

namespace Averias\RedisBloom\DataTypes;

use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\ParserTrait;
use Averias\RedisBloom\Validator\InputValidatorTrait;

class BaseDataType
{
    use InputValidatorTrait;
    use ParserTrait;

    /** @var string */
    protected $name;

    /** @var RedisClientAdapterInterface */
    protected $redisClientAdapter;

    public function __construct(string $filterName, RedisClientAdapterInterface $redisClientAdapter)
    {
        $this->name = $filterName;
        $this->redisClientAdapter = $redisClientAdapter;
    }

    /**
     * @param string $command
     * @param string $key
     * @param array $params
     * @return mixed
     * @throws ResponseException
     */
    protected function executeBloomCommand(string $command, string $key, array $params = [])
    {
        $response = $this->redisClientAdapter->executeBloomCommand($command, $key, $params);
        return $this->parseResponse($command, $response);
    }
}
