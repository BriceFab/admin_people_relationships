<?php

namespace App\Controller\Admin\Crud\Common;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

abstract class BaseCrudController extends AbstractCrudController
{

    public function getBlameableTimestampableFields(): array
    {
        return [
            FormField::addPanel('Informations systèmes')
                ->collapsible()->renderCollapsed(false),
            TextField::new('createdBy', 'entity.createdBy')
                ->onlyOnDetail(),
            DateTimeField::new('createdAt', 'entity.createdAt')
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt', 'entity.updatedAt')
                ->onlyOnDetail(),
            TextField::new('updatedBy', 'entity.updatedBy')
                ->onlyOnDetail(),
        ];
    }

}