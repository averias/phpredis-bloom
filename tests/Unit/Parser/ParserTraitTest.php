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

namespace Averias\RedisBloom\Tests\Unit\Parser;

use Averias\RedisBloom\Enum\BloomCommands;
use Averias\RedisBloom\Parser\ParserTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ParserTraitTest extends TestCase
{
    /**
     * @dataProvider getParseResponseDataProvider
     * @param string $command
     * @param $valueToParse
     * @param $expectedParsedValue
     */
    public function testParseResponse(string $command, $valueToParse, $expectedParsedValue)
    {
        /** @var MockObject|ParserTrait $mock */
        $mock = $this->getParserTraitMock();
        $this->assertSame($expectedParsedValue, $mock->parseResponse($command, $valueToParse));
    }

    /**
     * @dataProvider getParseRequestDataProvider
     * @param string $command
     * @param $valueToParse
     * @param $expectedParsedValue
     */
    public function testParseRequest(string $command, $valueToParse, $expectedParsedValue)
    {
        /** @var MockObject|ParserTrait $mock */
        $mock = $this->getParserTraitMock();
        $this->assertSame($expectedParsedValue, $mock->parseRequest($command, $valueToParse));
    }

    protected function getParserTraitMock(): MockObject
    {
        return $this->getMockForTrait(ParserTrait::class);
    }

    public function getParseResponseDataProvider(): array
    {
        return [
            [BloomCommands::BF_RESERVE, 'OK', true],
            [BloomCommands::BF_ADD, 0, false],
            [BloomCommands::BF_MADD, [0, 1], [false, true]],
            [BloomCommands::BF_INSERT, [1, 1], [true, true]],
            [BloomCommands::BF_EXISTS, 1, true],
            [BloomCommands::BF_MEXISTS, [1, 0], [true, false]],
            [BloomCommands::BF_LOADCHUNK, 'OK', true]
        ];
    }

    public function getParseRequestDataProvider(): array
    {
        return [
            [BloomCommands::BF_RESERVE, [], []]
        ];
    }
}
