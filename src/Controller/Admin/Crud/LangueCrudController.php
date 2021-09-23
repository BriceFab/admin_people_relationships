<?php

namespace App\Controller\Admin\Crud;

use App\Classes\Enum\EnumRoles;
use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\Langue;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LangueCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Langue::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(EnumRoles::ROLE_ADMIN)
            ->setEntityLabelInSingular('langue')
            ->setEntityLabelInPlural('langues');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('code', "langue.code")
            ->setRequired(true);
        yield TextField::new('langue', "langue.langue")
            ->setRequired(true);

        yield TextField::new('createdBy', 'entity.createdBy')->onlyOnDetail();
        yield DateTimeField::new('createdAt', 'entity.createdAt')->onlyOnDetail();
        yield DateTimeField::new('updateAt', 'entity.updatedAt')->onlyOnDetail();
        yield TextField::new('updatedBy', 'entity.updatedBy')->onlyOnDetail();
    }

}
