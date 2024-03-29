<?php

namespace Organisationsnummer\Tests;

use Organisationsnummer\Organisationsnummer;
use Organisationsnummer\OrganisationsnummerException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class OrganisationsnummerTest extends TestCase
{
    /** @var array|OrgNrData[] */
    private static ?array $data = null;

    protected static function init(): void
    {
        if (self::$data === null) {
            $data = file_get_contents(
                'https://raw.githubusercontent.com/organisationsnummer/meta/main/testdata/list.json'
            );
            self::$data = array_map(
                static fn(array $o) => new OrgNrData($o),
                json_decode($data, true, 5, JSON_THROW_ON_ERROR)
            );
        }
    }

    public static function allProvider(): array
    {
        self::init();
        return array_map(static fn(OrgNrData $o) => [$o->input => $o], self::$data);
    }

    public static function validProvider(): array
    {
        self::init();
        return array_map(
            static fn(OrgNrData $o) => [$o->input => $o],
            array_filter(self::$data, static fn($o) => $o->valid)
        );
    }

    public static function invalidProvider(): array
    {
        self::init();
        return array_map(
            static fn(OrgNrData $o) => [$o->input => $o],
            array_filter(self::$data, static fn($o) => !$o->valid)
        );
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidThrows(OrgNrData $input): void
    {
        $this->expectException(OrganisationsnummerException::class);
        Organisationsnummer::parse($input->shortFormat);
    }

    #[DataProvider('allProvider')]
    public function testValidateOrgNumbers(OrgNrData $input): void
    {
        self::assertEquals($input->valid, Organisationsnummer::valid($input->shortFormat));
        self::assertEquals($input->valid, Organisationsnummer::valid($input->longFormat));
    }

    #[DataProvider('validProvider')]
    public function testFormatWithOutSeparator(OrgNrData $input): void
    {
        self::assertEquals($input->shortFormat, Organisationsnummer::parse($input->shortFormat)->format(false));
        self::assertEquals($input->shortFormat, Organisationsnummer::parse($input->longFormat)->format(false));
    }

    #[DataProvider('validProvider')]
    public function testFormatWithSeparator(OrgNrData $input): void
    {
        self::assertEquals($input->longFormat, Organisationsnummer::parse($input->longFormat)->format(true));
        self::assertEquals(
            str_replace('+', '-', $input->longFormat),
            Organisationsnummer::parse($input->shortFormat)->format(true)
        );
    }

    #[DataProvider('validProvider')]
    public function testGetType(OrgNrData $input): void
    {
        self::assertEquals($input->type, Organisationsnummer::parse($input->longFormat)->type());
        self::assertEquals($input->type, Organisationsnummer::parse($input->shortFormat)->type());
    }

    #[DataProvider('validProvider')]
    public function testGetVat(OrgNrData $input): void
    {
        self::assertEquals($input->vatNumber, Organisationsnummer::parse($input->shortFormat)->vatNumber());
        self::assertEquals($input->vatNumber, Organisationsnummer::parse($input->longFormat)->vatNumber());
    }

    #[TestWith(['121212121212'])]
    #[TestWith(['121212-1212'])]
    public function testWithPersonnummer(string $input): void
    {
        $nr = Organisationsnummer::parse($input);
        self::assertEquals("Enskild firma", $nr->type());
        self::assertTrue($nr->isPersonnummer());
    }
}
