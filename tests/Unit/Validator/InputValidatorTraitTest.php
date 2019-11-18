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
     * @dataProvider getValidateScalarDataProvider
     * @param $value
     * @throws ResponseException
     */
    public function testValidateScalar($value): void
    {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateScalar($value, 'test param');
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getValidateScalarExceptionDataProvider
     * @param $value
     * @throws ResponseException
     */
    public function testValidateScalarException($value): void
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage("test param value must be string or number");
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateScalar($value, 'test param');
    }

    /**
     * @throws ResponseException
     */
    public function testValidateInteger(): void
    {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateInteger(13, 'test param');
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getValidateIntegerExceptionDataProvider
     * @param $value
     * @throws ResponseException
     */
    public function testValidateIntegerException($value): void
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage("test param value must be integer");
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateInteger($value, 'test param');
    }

    /**
     * @dataProvider getValidatePercentRateDataProvider
     * @param $value
     * @throws ResponseException
     */
    public function testValidatePercentRate($value): void
    {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validatePercentRate($value, 'test param');
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getValidatePercentRateExceptionDataProvider
     * @param $value
     * @package sting $errorMessage
     * @throws ResponseException
     */
    public function testValidatePercentRateException($value, string $errorMessage): void
    {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage($errorMessage);
        $mock = $this->getInputValidatorTraitMock();
        $mock->validatePercentRate($value, 'test param');
    }

    /**
     * @return MockObject|InputValidatorTrait
     */
    protected function getInputValidatorTraitMock(): MockObject
    {
        return $this->getMockForTrait(InputValidatorTrait::class);
    }

    public function getValidateScalarDataProvider(): array
    {
        return [
            ['foo'],
            [13],
            [12.5]
        ];
    }

    public function getValidateScalarExceptionDataProvider(): array
    {
        return [
            [[13, 12]],
            [true],
            [function () {}]
        ];
    }

    public function getValidateIntegerExceptionDataProvider(): array
    {
        return [
            [[13, 12]],
            [true],
            [12.3],
            ['foo']
        ];
    }

    public function getValidatePercentRateDataProvider(): array
    {
        return [
            [0.0001],
            [1.0],
            [0.235698]
        ];
    }

    public function getValidatePercentRateExceptionDataProvider(): array
    {
        return [
            [[13, 12], "test param value must be float"],
            [true, "test param value must be float"],
            ['foo', "test param value must be float"],
            [12.3, "test param value must be > 0.0 and <= 1.0, provided value 12.3"],
            [0.0, "test param value must be > 0.0 and <= 1.0, provided value 0.0"]
        ];
    }
}
