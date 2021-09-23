<?php

namespace App\Controller\Admin\Crud;

use App\Classes\Enum\EnumRoles;
use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\Parametre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class ParametreCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Parametre::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission(EnumRoles::ROLE_ADMIN)
            ->setEntityLabelInSingular('parametre')
            ->setEntityLabelInPlural('parametres');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->setPermission(Action::NEW, EnumRoles::ROLE_ADMIN)
            ->setPermission(Action::DELETE, EnumRoles::ROLE_ADMIN);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(TextFilter::new('cle', "param.cle"));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('cle', "param.cle"),
            TextField::new('valeur', "param.valeur"),
            TextEditorField::new('description', "param.description"),
            DateField::new('date_fin_validite', "param.date_fin_validite"),
            TextField::new('createdBy', 'entity.createdBy')->onlyOnDetail(),
            DateTimeField::new('createdAt', 'entity.createdAt')->onlyOnDetail(),
            DateTimeField::new('updateAt', 'entity.updatedAt')->onlyOnDetail(),
            TextField::new('updatedBy', 'entity.updatedBy')->onlyOnDetail(),
        ];
    }

}
