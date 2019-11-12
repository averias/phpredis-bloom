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

namespace Averias\RedisBloom\Parser;

use Averias\RedisBloom\Enum\RequestParser;
use Averias\RedisBloom\Enum\ResponseParser;

trait ParserTrait
{
    /**
     * @param string $command
     * @param mixed $input
     * @return mixed
     */
    protected function parseRequest(string $command, $input)
    {
        return $this->baseParse(RequestParser::COMMAND_PARSERS, $command, $input);
    }

    /**
     * @param string $command
     * @param mixed $response
     * @return mixed
     */
    protected function parseResponse(string $command, $response)
    {
        return $this->baseParse(ResponseParser::COMMAND_PARSERS, $command, $response);
    }

    /**
     * @param array $commandParsers
     * @param string $command
     * @param mixed $data
     * @return mixed
     */
    private function baseParse(array $commandParsers, string $command, $data)
    {
        if (isset($commandParsers[$command])) {
            $className = $commandParsers[$command];

            /** @var ParserInterface $parser */
            $parser = new $className();
            return $parser->parse($data);
        }

        return $data;
    }
}
