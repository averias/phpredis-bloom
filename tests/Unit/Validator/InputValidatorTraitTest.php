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
     * @dataProvider getValidateFloatRangeDataProvider
     * @param $value
     * @param float $minValue
     * @param float $maxValue
     * @throws ResponseException
     */
    public function testValidateRange($value, $minValue = 0.0, $maxValue = 1.0): void
    {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateRange($value, 'test param', $minValue, $maxValue);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getValidateFloatRangeInclusiveMaxDataProvider
     * @param $value
     * @param float $minValue
     * @param float $maxValue
     * @throws ResponseException
     */
    public function testValidateRangeInclusiveMax($value, $minValue = 0.0, $maxValue = 1.0): void
    {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateRangeInclusiveMax($value, 'test param', $minValue, $maxValue);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getValidateFloatRangeExceptionDataProvider
     * @param $value
     * @param string $errorMessage
     * @param $minValue
     * @param $maxValue
     * @throws ResponseException
     */
    public function testValidateRangeException(
        $value,
        string $errorMessage,
        $minValue = 0.0,
        $maxValue = 1.0
    ): void {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage($errorMessage);
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateRange($value, 'test param', $minValue, $maxValue);
    }

    /**
     * @dataProvider getValidateFloatRangeInclusiveMaxExceptionDataProvider
     * @param $value
     * @param string $errorMessage
     * @param $minValue
     * @param $maxValue
     * @throws ResponseException
     */
    public function testValidateRangeInclusiveMaxException(
        $value,
        string $errorMessage,
        $minValue = 0.0,
        $maxValue = 1.0
    ): void {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage($errorMessage);
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateRangeInclusiveMax($value, 'test param', $minValue, $maxValue);
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

    public function getValidateFloatRangeDataProvider(): array
    {
        return [
            [0.0001],
            [1.0, 0.9, 1.01],
            [10, 9, 11],
            [1.235699, 1.235698, 7.23]
        ];
    }

    public function getValidateFloatRangeExceptionDataProvider(): array
    {
        return [
            [12.3, "test param value must be > 0.0 and < 1.0, provided value 12.300000"],
            [1.0, "test param value must be > 0.0 and < 1.0, provided value 1.000000"],
            [10, "test param value must be > 10.0 and < 20.0, provided value 10.000000", 10, 20]
        ];
    }


    public function getValidateFloatRangeInclusiveMaxDataProvider(): array
    {
        return [
            [1.0],
            [1.01, 0.9, 1.01],
            [11, 9, 11],
            [1.235699, 1.235698, 7.23]
        ];
    }

    public function getValidateFloatRangeInclusiveMaxExceptionDataProvider(): array
    {
        return [
            [12.3, "test param value must be > 0.0 and <= 1.0, provided value 12.300000"],
            [1.01, "test param value must be > 0.0 and <= 1.0, provided value 1.010000"],
            [10, "test param value must be > 10.0 and <= 20.0, provided value 10.000000", 10, 20]
        ];
    }
}
