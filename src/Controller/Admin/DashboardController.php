<?php

namespace App\Controller\Admin;

use App\Classes\Enum\EnumRoles;
use App\Controller\Admin\Crud\ActionLogCrudController;
use App\Controller\Admin\Crud\ExceptionLogCrudController;
use App\Controller\Admin\Crud\ImageCrudController;
use App\Controller\Admin\Crud\LangueCrudController;
use App\Controller\Admin\Crud\LocationCrudController;
use App\Controller\Admin\Crud\ParametreCrudController;
use App\Controller\Admin\Crud\PaysCrudController;
use App\Controller\Admin\Crud\RegionCrudController;
use App\Controller\Admin\Crud\ServiceCrudController;
use App\Controller\Admin\Crud\TraductionCrudController;
use App\Controller\Admin\Crud\UserCrudController;
use App\Controller\Admin\Crud\VilleCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractDashboardController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/%ADMIN_PATH%", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->translator->trans('site.name'))
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        if ($this->isGranted(EnumRoles::ROLE_DEV)) {
            yield MenuItem::section('admin.section.config');

            yield MenuItem::linkToCrud('admin.menu.user', 'fa fa-user', UserCrudController::getEntityFqcn());
            yield MenuItem::linkToCrud('admin.menu.param', 'fas fa-cog', ParametreCrudController::getEntityFqcn());

            yield MenuItem::subMenu('admin.menu.multi_langues', 'fas fa-language')->setSubItems([
                MenuItem::linkToCrud('admin.menu.traduction', 'fas fa-language', TraductionCrudController::getEntityFqcn()),
                MenuItem::linkToCrud('admin.menu.langues', 'fas fa-globe-europe', LangueCrudController::getEntityFqcn()),
            ]);
        }

        if ($this->isGranted(EnumRoles::ROLE_DEV)) {
            yield MenuItem::section('admin.section.history');

            yield MenuItem::subMenu('admin.menu.logs', 'fa fa-history')->setSubItems([
                MenuItem::linkToCrud('admin.menu.logs.action', null, ActionLogCrudController::getEntityFqcn()),
                MenuItem::linkToCrud('admin.menu.logs.exception', null, ExceptionLogCrudController::getEntityFqcn())
            ]);
        }
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linktoRoute('menu.user.profile', 'fa fa-user-circle-o', 'mon_profil')
            ]);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->addFormTheme("@FOSCKEditor/Form/ckeditor_widget.html.twig")
            ->addFormTheme('@FMElfinder/Form/elfinder_widget.html.twig')
            ->addFormTheme('form_theme/fields.html.twig');
    }

}
