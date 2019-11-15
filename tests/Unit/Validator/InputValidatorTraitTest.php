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

namespace Averias\RedisBloom\Tests\Unit\Validator;

use Averias\RedisBloom\Exception\ResponseException;
use Averias\RedisBloom\Validator\InputValidatorTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InputValidatorTraitTest extends TestCase
{
    /**
     * @dataProvider getDataProvider
     * @param $value
     * @throws ResponseException
     */
    public function testValidateScalar($value): void
    {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateScalar($value);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getExceptionDataProvider
     * @param $value
     * @throws ResponseException
     */
    public function testValidateScalarException($value): void
    {
        $this->expectException(ResponseException::class);
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateScalar($value);
    }

    /**
     * @return MockObject|InputValidatorTrait
     */
    protected function getInputValidatorTraitMock(): MockObject
    {
        return $this->getMockForTrait(InputValidatorTrait::class);
    }

    public function getDataProvider(): array
    {
        return [
            ['foo'],
            [13],
            [12.5]
        ];
    }

    public function getExceptionDataProvider(): array
    {
        return [
            [[13, 12]],
            [true],
            [function () {}]
        ];
    }
}
