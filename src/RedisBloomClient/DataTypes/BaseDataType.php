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
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\ParserTrait;
use Averias\RedisBloom\Validator\InputValidatorTrait;
use Exception;

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

    /**
     * @param string $key
     * @param string $message
     * @return int
     * @throws ResponseException
     */
    protected function deleteKey(string $key, string $message = ""): int
    {
        try {
            $deleted = $this->redisClientAdapter->executeCommandByName('del', [$key]);
        } catch (Exception $exception) {
            if ($message !== "") {
                $message .= ", ";
            }
            throw new ResponseException(
                sprintf("%s%s key could NOT be deleted, please delete it manually.", $message, $key)
            );
        }

        return $deleted;
    }

    /**
     * @param string $targetFilter
     * @param string $exceptionMessage
     * @throws ResponseException
     */
    protected function copyFailedException(string $targetFilter, string $exceptionMessage)
    {
        $message = sprintf(
            "copying data to '%s' target filter failed, reason %s",
            $targetFilter,
            $exceptionMessage
        );
        $this->deleteKey($targetFilter, $message);
        throw new ResponseException($message . ", target filter was deleted");
    }
}
