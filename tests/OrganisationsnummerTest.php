<?php

namespace Organisationsnummer\Tests;

use Organisationsnummer\Organisationsnummer;
use PHPUnit\Framework\TestCase;

class OrganisationsnummerTest extends TestCase
{
    /**
     * @testWith ["556016-0680", true]
     *           ["556103-4249", true]
     *           ["5561034249", true]
     *           ["556016-0681", false]
     *           ["556103-4250", false]
     *           ["5561034250", false]
     *           ["559244-0001", true]
     */
    public function testValidateOrgNumbers(string $number, bool $expected): void
    {
        self::assertEquals($expected, Organisationsnummer::valid($number));
    }

    /**
     * @testWith ["556016-0680", "5560160680"]
     *           ["556103-4249", "5561034249"]
     *           ["5561034249", "5561034249"]
     *           ["901211-9948", "9012119948"]
     *           ["9012119948", "9012119948"]
     */
    public function testFormatWithOutSeparator(string $input, string $expected): void
    {
        self::assertEquals($expected, Organisationsnummer::parse($input)->format(false));
    }

    /**
     * @testWith ["556016-0680", "556016-0680"]
     *           ["556103-4249", "556103-4249"]
     *           ["5561034249", "556103-4249"]
     *           ["9012119948", "901211-9948"]
     *           ["901211-9948", "901211-9948"]
     */
    public function testFormatWithSeparator(string $input, string $expected): void
    {
        self::assertEquals($expected, Organisationsnummer::parse($input)->format(true));
    }

    /**
     * @testWith ["556016-0680", "Aktiebolag"]
     *           ["556103-4249", "Aktiebolag"]
     *           ["5561034249", "Aktiebolag"]
     *           ["8510033999", "Enskild firma"]
     */
    public function testGetType(string $input, string $expected): void
    {
        self::assertEquals($expected, Organisationsnummer::parse($input)->type());
    }

    /**
     * @testWith ["556016-0680", "SE556016068001"]
     *           ["556103-4249", "SE556103424901"]
     *           ["5561034249", "SE556103424901"]
     *           ["9012119948", "SE901211994801"]
     *           ["19901211-9948", "SE901211994801"]
     */
    public function testGetVat(string $input, string $expected): void
    {
        self::assertEquals($expected, Organisationsnummer::parse($input)->vatNumber());
    }

    /**
     * @testWith ["121212121212"]
     *           ["121212-1212"]
     */
    public function testWithPersonnummer(string $input): void
    {
        $nr = Organisationsnummer::parse($input);
        self::assertEquals("Enskild firma", $nr->type());
        self::assertTrue($nr->isPersonnummer());
    }
}
