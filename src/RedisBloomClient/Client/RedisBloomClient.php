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

namespace Averias\RedisBloom\Client;

use Averias\RedisBloom\Command\Traits\BloomCommandTrait;
use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Adapter\RedisClientAdapterInterface;
use Averias\RedisBloom\Factory\RequestParserFactoryInterface;
use Averias\RedisBloom\Validator\InputValidatorInterface;

class RedisBloomClient implements RedisBloomClientInterface
{
    use BloomCommandTrait;

    /** @var RedisClientAdapterInterface */
    protected $redisClientAdapter;

    /** @var InputValidatorInterface */
    protected $inputValidator;

    /** @var RequestParserFactoryInterface */
    protected $requestParserFactory;

    public function __construct(
        RedisClientAdapterInterface $redisClientAdapter,
        InputValidatorInterface $inputValidator,
        RequestParserFactoryInterface $requestParserFactory
    ) {
        $this->redisClientAdapter = $redisClientAdapter;
        $this->inputValidator = $inputValidator;
        $this->requestParserFactory = $requestParserFactory;
    }

    /**
     * @param string $commandName
     * @param array $arguments
     * @return mixed
     */
    public function executeRawCommand(string $commandName, ...$arguments)
    {
        return $this->redisClientAdapter->executeRawCommand($commandName, ...$arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ResponseException
     */
    public function __call(string $name, array $arguments)
    {
        return $this->redisClientAdapter->executeCommandByName($name, $arguments);
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
        return $this->redisClientAdapter->executeBloomCommand($command, $key, $params);
    }

    /**
     * @param $value
     * @throws ResponseException
     */
    protected function validateScalar($value): void
    {
        $this->inputValidator->validateScalar($value);
    }

    /**
     * @return RequestParserFactoryInterface
     */
    protected function getRequestParserFactory(): RequestParserFactoryInterface
    {
        return $this->requestParserFactory;
    }
}
