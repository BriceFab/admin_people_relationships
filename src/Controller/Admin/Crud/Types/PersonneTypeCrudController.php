<?php

namespace App\Controller\Admin\Crud\Types;

use App\Controller\Admin\Crud\Common\TypeCrudController;
use App\Entity\PersonneType;

class PersonneTypeCrudController extends TypeCrudController
{
    public static function getEntityFqcn(): string
    {
        return PersonneType::class;
    }
}
