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

namespace Averias\RedisBloom\Tests\Unit\Parser\Response;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Parser\Response\OkToTrue;
use PHPUnit\Framework\TestCase;

class OkToTrueTest extends TestCase
{
    public function testResponseIsNotStringException():void
    {
        $this->expectException(ResponseException::class);
        $parser = new OkToTrue();
        $parser->parse(false);
    }

    public function testResponseIsNotStringOKException():void
    {
        $this->expectException(ResponseException::class);
        $parser = new OkToTrue();
        $parser->parse('KO');
    }

    public function testParse(): void
    {
        $parser = new OkToTrue();
        $result = $parser->parse('OK');
        $this->assertTrue($result);
    }
}
