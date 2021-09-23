<?php

namespace App\Controller\Admin\Crud\Types;

use App\Controller\Admin\Crud\Common\TypeCrudController;
use App\Entity\AdresseType;
use App\Entity\TelephoneType;

class AdresseTypeCrudController extends TypeCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdresseType::class;
    }
}
