<?php
use \Mockery;

use \Acme\Validation\ServiceProvider as ValidationServiceProvider;

use \Acme\Validation\Validator as V;
use \Acme\Validation\Exception as ValidationException;

class FooValidator extends V {}

class CustomValidatorsTest extends TestCase
{
	protected $validator;

	public function testAlphaNumUnderscore()
	{
		$this->runValidationTests([
			'success' => [
				'',
				' ',
				'abc123ABC_',
				'a',
				'A',
				'0',
				'1',
				'_',
				'12ab__',
				'12__ab',
				'ab__12',
				'ab12__',
				'__ab12',
				'__12ab',
				'_1a_A3zZ2_7Z__aBC',
				'a_b_c_',
			],
			'failure' => [
				'-',
				'a ',
				'abc 123',
				'abc-123',
				'abc!123',
				'!@#$%^&*()-+=[]{};:"<>,./?\\|`~'
			],
		],
		'alpha_num_underscore');
	}

	public function testNoConsecutiveUnderscores()
	{
		$this->runValidationTests([
			'success' => [
				'',
				' ',
				'_',
				'abc',
				'a-b-c',
				'a_b_c_',
				'_a-b_c--d_',
			],
			'failure' => [
				'__',
				'a__',
				'__b',
				'a__b',
				'a_b__',
				'a_b__c',
			],
		],
		'no_consecutive_underscores');
	}

	public function runValidationTests(array $test_values, $validator_name)
	{
		$this->validator->rules = ['foo' => $validator_name];

		foreach ($test_values['success'] as $success_value)
		{
			try
			{
				$this->validator->validate(['foo' => $success_value]);
				$this->assertTrue(true);
			}
			catch (ValidationException $e)
			{
				$this->fail("The value '$success_value' should have passed the $validator_name validator, but was failed.");
			}
		}

		foreach ($test_values['failure'] as $failure_value)
		{
			try
			{
				$this->validator->validate(['foo' => $failure_value]);
				$this->fail("The value '$failure_value' should have failed the $validator_name validator, but was passed.");
			}
			catch (ValidationException $e)
			{
				$this->assertTrue(true);
			}
		}
	}

	public function setUp()
	{
		parent::setUp();
		$this->validator = new FooValidator;
	}

	public function tearDown()
	{
		parent::tearDown();
	}
}
