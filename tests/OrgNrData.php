<?php

namespace Organisationsnummer\Tests;

class OrgNrData
{
    public function __construct(array $o)
    {
        $this->input = $o['input'];
        $this->longFormat = $o['long_format'];
        $this->shortFormat = $o['short_format'];
        $this->valid = $o['valid'];
        $this->type = $o['type'];
        $this->vatNumber = $o['vat_number'];
    }

    public string $input;
    public string $longFormat;
    public string $shortFormat;
    public bool $valid;
    public string $type;
    public string $vatNumber;
}
