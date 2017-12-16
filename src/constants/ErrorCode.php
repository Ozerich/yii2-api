<?php

namespace blakit\api\constants;

use MyCLabs\Enum\Enum;

class ErrorCode extends Enum
{
    const UNKNOWN_ERROR = 'UNKNOWN_ERROR';

    const FIELD_REQUIRED = 'FIELD_REQUIRED';
    const FIELD_UNIQUE = 'FIELD_UNIQUE';
    const FIELD_INCORRECT_EMAIL = 'FIELD_INCORRECT_EMAIL';
    const FIELD_SIMPLE_PASSWORD = 'FIELD_SIMPLE_PASSWORD';
}