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
     * @param bool $isExclusiveMin
     * @param float $maxValue
     * @param bool $isExclusiveMax
     * @throws ResponseException
     */
    public function testValidateFloatRange(
        $value,
        $minValue = 0.0,
        $isExclusiveMin = false,
        $maxValue = 1.0,
        $isExclusiveMax = false
    ): void {
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateFloatRange($value, 'test param', $minValue, $isExclusiveMin, $maxValue, $isExclusiveMax);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider getValidateFloatRangeExceptionDataProvider
     * @param $value
     * @param string $errorMessage
     * @param float $minValue
     * @param bool $isExclusiveMin
     * @param float $maxValue
     * @param bool $isExclusiveMax
     * @throws ResponseException
     */
    public function testValidateFloatRangeException(
        $value,
        string $errorMessage,
        $minValue = 0.0,
        $isExclusiveMin = false,
        $maxValue = 1.0,
        $isExclusiveMax = false
    ): void {
        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage($errorMessage);
        $mock = $this->getInputValidatorTraitMock();
        $mock->validateFloatRange($value, 'test param', $minValue, $isExclusiveMin, $maxValue, $isExclusiveMax);
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
            [1.0, 0.9, true, 1.0, false],
            [1.235698, 1.235698, false, 7.23, true]
        ];
    }

    public function getValidateFloatRangeExceptionDataProvider(): array
    {
        return [
            [[13, 12], "test param value must be float"],
            [true, "test param value must be float"],
            ['foo', "test param value must be float"],
            [12.3, "test param value must be >= 0.0 and <= 1.0, provided value 12.3"],
            [0.0, "test param value must be > 0.0 and <= 1.0, provided value 0.000000", 0.0, true]
        ];
    }
}
