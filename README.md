# organisationsnummer
[![Test](https://github.com/organisationsnummer/php/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/organisationsnummer/php/actions/workflows/test.yml)

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

See [OrganisationsnummerTest.php](https://github.com/organisationsnummer/php/blob/master/tests/OrganisationsnummerTest.php) for more examples.

## License

MIT
