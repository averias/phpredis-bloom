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

namespace Averias\RedisBloom\Tests\Parser\Response;

use Averias\RedisBloom\Parser\Response\IntegerToBool;
use PHPUnit\Framework\TestCase;
use Throwable;

class IntegerToBoolTest extends TestCase
{
    public function testResponseIsNotIntegerException():void
    {
        $this->expectException(Throwable::class);
        $parser = new IntegerToBool();
        $parser->parse('foo');
    }

    /**
     * @dataProvider getDataProvider
     * @param int $response
     * @param bool $expected
     */
    public function testParse(int $response, bool $expected): void
    {
        $parser = new IntegerToBool();
        $result = $parser->parse($response);
        $this->assertSame($expected, $result);
    }

    public function getDataProvider(): array
    {
        return [
            [1, true],
            [0, false]
        ];
    }
}
