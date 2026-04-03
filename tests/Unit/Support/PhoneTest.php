<?php

namespace Tests\Unit\Support;

use App\Support\Phone;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    #[Test]
    /**
     * Scenario 1: Local & Regional Brazilian Numbers
     */
    public function it_validates_brazilian_local_and_regional_numbers(): void
    {
        // Local (8 digits)
        $this->assertTrue(Phone::isValid('3212-4455'));
        $this->assertTrue(Phone::isValid('99122-3344'));

        // Regional (10-11 digits)
        $this->assertTrue(Phone::isValid('1132124455'));
        $this->assertTrue(Phone::isValid('11991223344'));
    }

    #[Test]
    /**
     * Scenario 2: Long Format DDI Boundaries (Brazilian vs International)
     */
    public function it_validates_brazilian_international_format(): void
    {
        // Valid Brazilian with 55 (12-13 digits)
        $this->assertTrue(Phone::isValid('551132124455'));
        $this->assertTrue(Phone::isValid('5511991223344'));

        // International 12-13 digits NOT starting with 55 should be treated as International
        // If it follows international rules, it should pass without BR regex checks
        $this->assertTrue(Phone::isValid('120255501234')); // 12 digits non-BR
    }

    #[Test]
    public function it_validates_brazilian_strict_rules(): void
    {
        // Valid (BR)
        $this->assertTrue(Phone::isValid('5541991899653'));
        $this->assertTrue(Phone::isValid('11991899654'));
        $this->assertTrue(Phone::isValid('991899653'));
        $this->assertTrue(Phone::isValid('40917488'));
        $this->assertTrue(Phone::isValid('1440917488'));
        $this->assertTrue(Phone::isValid('554140917488'));

        // Invalid (BR)
        $this->assertFalse(Phone::isValid('4091786')); // 7 digits (< 8)
        $this->assertFalse(Phone::isValid('414091748')); // 9 digits, but starts with 4 (not 9)
        $this->assertFalse(Phone::isValid('554199189987')); // 55+41 + 8 digits starting with 9
        $this->assertFalse(Phone::isValid('55414091767')); // 55+41 + 7 digits
    }

    #[Test]
    public function it_rejects_invalid_and_garbage_inputs(): void
    {
        $this->assertFalse(Phone::isValid('12345')); // Too short
        $this->assertFalse(Phone::isValid('abc-1234')); // Contains letters
        $this->assertFalse(Phone::isValid('!@#$%^&*()')); // Only symbols
    }

    #[Test]
    public function it_normalizes_numbers_for_database(): void
    {
        // Local (8 digits) -> 55 + 41 + digits
        $this->assertEquals('554132124455', Phone::toDatabase('3212-4455'));

        // Regional (11 digits) -> 55 + digits
        $this->assertEquals('5511991223344', Phone::toDatabase(' (11) 99122-3344 '));

        // Brazil with DDI (13 digits) -> keep raw
        $this->assertEquals('551132124455', Phone::toDatabase('+55 (11) 3212-4455'));
    }

    #[Test]
    public function it_formats_numbers_for_display(): void
    {
        // Brazilian (Full)
        $this->assertEquals('+55 (11) 99122-3344', Phone::toHuman('5511991223344'));

        // Brazilian (Local/Regional via toDatabase then toHuman)
        $this->assertEquals('+55 (41) 3212-4455', Phone::toHuman('554132124455'));

        // International
        $this->assertEquals('+120255501234', Phone::toHuman('120255501234'));
    }
}
