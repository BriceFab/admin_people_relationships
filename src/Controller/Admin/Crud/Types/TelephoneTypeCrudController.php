<?php

namespace App\Controller\Admin\Crud\Types;

use App\Controller\Admin\Crud\Common\TypeCrudController;
use App\Entity\TelephoneType;

class TelephoneTypeCrudController extends TypeCrudController
{
    public static function getEntityFqcn(): string
    {
        return TelephoneType::class;
    }
}
