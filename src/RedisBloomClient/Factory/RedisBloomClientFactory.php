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

namespace Averias\RedisBloom\Factory;

use Averias\RedisBloom\Client\RedisBloomClient;
use Averias\RedisBloom\Client\RedisBloomClientInterface;
use Averias\RedisBloom\Exception\RedisClientException;
use Averias\RedisBloom\Validator\InputValidator;
use Averias\RedisBloom\Validator\RedisClientValidator;
use Averias\RedisBloom\Adapter\AdapterProvider;
use Exception;

class RedisBloomClientFactory implements RedisBloomClientFactoryInterface
{
    /** @var AdapterProvider */
    protected $adapterProvider;

    public function __construct()
    {
        $this->adapterProvider = new AdapterProvider(new RedisClientValidator());
    }

    /**
     * @param array|null $config
     * @return RedisBloomClientInterface
     * @throws RedisClientException
     */
    public function createClient(array $config = []): RedisBloomClientInterface
    {
        try {
            $adapter = $this->adapterProvider->get($config);
        } catch (Exception $e) {
            throw new RedisClientException($e->getMessage());
        }

        return new RedisBloomClient($adapter, new InputValidator(), new RequestParserFactory());
    }
}