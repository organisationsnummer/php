<?php

namespace Organisationsnummer;

use Exception;
use Personnummer\Personnummer;
use Personnummer\PersonnummerException;

class Organisationsnummer
{
    private const FIRMA_TYPES = [
        // String to map different company types.
        // Will only pick 0-9, but we use 10 to be EF as we want it constant.
        'Okänt', // 0
        'Dödsbon', // 1
        'Stat, landsting, kommun eller församling', // 2
        'Utländska företag som bedriver näringsverksamhet eller äger fastigheter i Sverige', // 3
        'Okänt', // 4
        'Aktiebolag', // 5
        'Enkelt bolag', // 6
        'Ekonomisk förening eller bostadsrättsförening', // 7
        'Ideella förening och stiftelse', // 8
        'Handelsbolag, kommanditbolag och enkelt bolag', // 9
        'Enskild firma', // 10
    ];

    private const REGEX = '/^(\d{2}){0,1}(\d{2})(\d{2})(\d{2})([-+]?)?(\d{4})$/';

    //region static

    private static function luhnCheck(string $value): bool
    {
        $sum = 0;
        $len = strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $v = (int)$value[$i];
            $v *= 2 - ($i % 2);

            if ($v > 9) {
                $v -= 9;
            }

            $sum += $v;
        }

        return $sum % 10 === 0;
    }

    /**
     * Parse a string to an organisationsnummer.
     *
     * @param string $input Organisationsnummer as string to base the object on.
     * @return Organisationsnummer Organisationsnummer as an object
     * @throws OrganisationsnummerException On Parse Error.
     */
    public static function parse(string $input): Organisationsnummer
    {
        return new Organisationsnummer($input);
    }

    /**
     * Validates a string as a organisationsnummer.
     *
     * @param string $input Organisationsnummer as string to validate.
     * @return bool True on valid Organisationsnummer.
     */
    public static function valid(string $input): bool
    {
        try {
            self::parse($input);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //endregion

    private ?Personnummer $innerPersonnummer = null;
    private string $number = "";

    /**
     * Organisationsnummer constructor.
     *
     * @param string $input Organisationsnummer as string to base the object on.
     * @throws OrganisationsnummerException On parse error.
     */
    public function __construct(string $input)
    {
        $this->innerParse($input);
    }

    /**
     * Get organisation VAT.
     *
     * @return string
     */
    public function vatNumber(): string
    {
        return sprintf('SE%s01', $this->getShortString());
    }

    /**
     * Format the Organisationsnummer and return it as a string.
     *
     * @param bool $separator If to include separator (-) or not.
     * @return string
     */
    public function format(bool $separator = true): string
    {
        if ($separator && $this->isPersonnummer()) {
            /** @noinspection NullPointerExceptionInspection */
            return $this->personnummer()->format(false);
        }

        $number = $this->getShortString();
        return $separator ? sprintf('%s-%s', substr($number, 0, 6), substr($number, 6)) : $number;
    }

    /**
     * Determine is the organisationsnummer is a personnummer or not.
     *
     * @return bool
     */
    public function isPersonnummer(): bool
    {
        return $this->innerPersonnummer !== null;
    }

    /**
     * Get Personnummer instance (if IsPersonnummer).
     *
     * @return Personnummer|null
     */
    public function personnummer(): ?Personnummer
    {
        return $this->innerPersonnummer;
    }

    /**
     * Get type of company/firm.
     *
     * @return string
     */
    public function type(): string
    {
        return $this->isPersonnummer() ? self::FIRMA_TYPES[10] : self::FIRMA_TYPES[(int)$this->number[0]];
    }

    //region Private

    private function innerParse(string $input): void
    {
        $inputLength = strlen($input);
        if ($inputLength < 10 || $inputLength > 13) {
            throw new OrganisationsnummerException("Input value too " . ($inputLength > 13 ? "long" : "short"));
        }

        $originalInput = $input;
        try {
            $matches = [];
            preg_match(self::REGEX, $input, $matches);

            if (empty($matches)) {
                throw new OrganisationsnummerException();
            }

            if ($matches[1] !== "") {
                if ((int)$matches[1] !== 16) {
                    throw new OrganisationsnummerException();
                }
                $input = substr($input, 2);
            }

            $input = str_replace(['+', '-'], ['', ''], $input);
            if ((int)$matches[3] < 20 || (int)$matches[2] < 10 || !self::luhnCheck($input)) {
                throw new OrganisationsnummerException();
            }

            $this->number = $input;
        } catch (Exception $ex) {
            try {
                $this->innerPersonnummer = Personnummer::parse($originalInput);
            } catch (PersonnummerException $_) {
                throw $ex;
            }
        }
    }

    private function getShortString(): string
    {
        $asString = $this->isPersonnummer() ? $this->innerPersonnummer->format(false) : $this->number;
        return str_replace(['+', '-'], ['', ''], $asString);
    }

    //endregion
}
