<?php

namespace App\Classes\Enum;

use App\Classes\Traits\EnumTrait;

class EnumRoles
{
    use EnumTrait;

    const ROLE_PANEL_ADMIN = "ROLE_PANEL_ADMIN";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_DEV = "ROLE_DEV";
}