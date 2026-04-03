<?php

namespace Tests\Feature\Rules;

use App\Rules\ValidPhone;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidPhoneRuleTest extends TestCase
{
    #[Test]
    public function it_passes_validation_for_valid_brazilian_phones(): void
    {
        $data = ['phone' => '11991223344'];
        $rules = ['phone' => [new ValidPhone]];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function it_passes_validation_for_valid_international_phones(): void
    {
        $data = ['phone' => '120255501234'];
        $rules = ['phone' => [new ValidPhone]];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    /**
     * Scenario 4: Validation Rule Adapter Testing
     */
    public function it_fails_validation_for_invalid_phones(): void
    {
        $data = ['phone' => 'abc-1234'];
        $rules = ['phone' => [new ValidPhone]];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            'The provided phone is invalid.',
            $validator->errors()->first('phone')
        );
    }
}
