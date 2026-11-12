# organisationsnummer
[![Test](https://github.com/organisationsnummer/php/actions/workflows/test.yml/badge.svg?branch=main)](https://github.com/organisationsnummer/php/actions/workflows/test.yml)

Validate Swedish organization numbers. 

Follows version 1.1 of the [specification](https://github.com/organisationsnummer/meta#package-specification-v11).

Install the package:

```
composer require organisationsnummer/organisationsnummer
```

## Example

```php
use Organisationsnummer\Organisationsnummer;

Organisationsnummer::valid('202100-5489');
// => true
```

See [OrganisationsnummerTest.php](https://github.com/organisationsnummer/php/blob/main/tests/OrganisationsnummerTest.php) for more examples.

## In memoriam

Fredrik "Frozzare" Forsmo (1991-2026) was the initiator, co-founder and a core contributor of the organisationsnummer project. This library carries his work. He is missed.

## License

MIT
