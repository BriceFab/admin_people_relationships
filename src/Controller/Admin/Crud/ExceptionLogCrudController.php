<?php

namespace App\Controller\Admin\Crud;

use App\Classes\Enum\EnumRoles;
use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\ExceptionLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class ExceptionLogCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return ExceptionLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('exceptionLog')
            ->setEntityLabelInPlural('exceptionLogs')
            ->setEntityPermission(EnumRoles::ROLE_ADMIN)
            ->setSearchFields(['id', 'method', 'uri', 'exceptionMessage', 'exceptionCode', 'ip', 'user'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, EnumRoles::ROLE_ADMIN)
            ->disable(Action::NEW, Action::EDIT);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('method', 'exceptionLog.method'))
            ->add(TextFilter::new('uri', 'exceptionLog.uri'))
            ->add(TextFilter::new('ip', 'exceptionLog.ip'))
            ->add(DateTimeFilter::new('createdAt', 'entity.createdAt'))
            ->add(TextFilter::new('exceptionMessage', 'exceptionLog.exceptionMessage'))
            ->add(TextFilter::new('exceptionCode', 'exceptionLog.responseCode'))
            ->add(TextFilter::new('user', 'exceptionLog.user'));
    }

    public function configureFields(string $pageName): iterable
    {
        $method = TextField::new('method', 'exceptionLog.method');
        $uri = TextareaField::new('uri', 'exceptionLog.uri');
        $exceptionMessage = TextareaField::new('exceptionMessage', 'exceptionLog.exceptionMessage');
        $exceptionCode = IntegerField::new('exceptionCode', 'exceptionLog.exceptionCode');
        $ip = TextField::new('ip', 'exceptionLog.ip');
        $user = TextField::new('user', 'exceptionLog.user');
        $id = IntegerField::new('id');
        $createdAt = DateTimeField::new('createdAt', 'entity.createdAt');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$exceptionCode, $createdAt, $method, $uri->setMaxLength(100), $ip, $user];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $method, $uri, $exceptionMessage, $exceptionCode, $createdAt, $ip, $user];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$method, $uri, $exceptionMessage, $exceptionCode, $createdAt, $ip, $user];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$method, $uri, $exceptionMessage, $exceptionCode, $createdAt, $ip, $user];
        }

        return [];
    }
}
