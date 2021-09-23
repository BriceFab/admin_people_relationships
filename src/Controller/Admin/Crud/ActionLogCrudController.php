<?php

namespace App\Controller\Admin\Crud;

use App\Classes\Enum\EnumRoles;
use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\ActionLog;
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

class ActionLogCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActionLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('actionLog')
            ->setEntityLabelInPlural('actionLogs')
            ->setEntityPermission(EnumRoles::ROLE_ADMIN)
            ->setSearchFields(['id', 'method', 'uri', 'ip', 'responseCode', 'user'])
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
            ->add(TextFilter::new('method', 'actionLog.method'))
            ->add(TextFilter::new('uri', 'actionLog.uri'))
            ->add(TextFilter::new('ip', 'actionLog.ip'))
            ->add(DateTimeFilter::new('request_at', 'actionLog.request_at'))
            ->add(TextFilter::new('responseCode', 'actionLog.responseCode'))
            ->add(TextFilter::new('user', 'actionLog.user'));
    }

    public function configureFields(string $pageName): iterable
    {
        $method = TextField::new('method', 'actionLog.method');
        $uri = TextareaField::new('uri', 'actionLog.uri');
        $ip = TextField::new('ip', 'actionLog.ip');
        $requestAt = DateTimeField::new('request_at', 'actionLog.request_at');
        $responseCode = IntegerField::new('responseCode', 'actionLog.responseCode');
        $user = TextField::new('user', 'actionLog.user');
        $id = IntegerField::new('id');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$responseCode, $requestAt, $method, $uri->setMaxLength(100), $ip, $user];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $method, $uri, $ip, $requestAt, $responseCode, $user];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$method, $uri, $ip, $requestAt, $responseCode, $user];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$method, $uri, $ip, $requestAt, $responseCode, $user];
        }

        return [];
    }
}
