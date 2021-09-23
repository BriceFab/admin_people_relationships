<?php

namespace App\Controller\Admin\Crud\Common;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

abstract class TypeCrudController extends BaseCrudController
{

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular("Type")
            ->setEntityLabelInPlural("Types");
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new("nom", "Nom"),
        ];
    }

}