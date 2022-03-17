<?php
namespace Organisationsnummer;

use Personnummer\Personnummer;

class Organisationsnummer {

    public static function parse(string $input): Organisationsnummer {}
    public static function valid(string $input): bool {}

    public function vatNumber(): string {}
    public function format(bool $separator = true): string {}
    public function isPersonnummer(): bool {}
    public function personnummer(): Personnummer {}
    public function type(): string {}

}
