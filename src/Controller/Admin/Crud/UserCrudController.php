<?php

namespace App\Controller\Admin\Crud;

use App\Classes\Enum\EnumRoles;
use App\Config\ConstraintsConfig;
use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\User;
use App\Helper\SecurityHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserCrudController extends BaseCrudController
{
    private TranslatorInterface $translator;
    private SecurityHelper $securityHelper;

    public function __construct(TranslatorInterface $translator, SecurityHelper $securityHelper)
    {
        $this->translator = $translator;
        $this->securityHelper = $securityHelper;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles_choices = EnumRoles::getChoices("user.role", [
            EnumRoles::ROLE_PANEL_ADMIN,
            EnumRoles::ROLE_ADMIN,
        ]);

        if ($this->isGranted(EnumRoles::ROLE_ADMIN) || ($pageName === Crud::PAGE_INDEX || $pageName === Crud::PAGE_DETAIL)) {
            //Peut ajouter les rôles entreprise informatique qui si est au moins entreprise informatique
            EnumRoles::addListChoice($roles_choices, EnumRoles::ROLE_ADMIN, "user.role");
        }

        $roles = ChoiceField::new('roles', 'user.roles')
            ->setChoices($roles_choices)
            ->allowMultipleChoices()
            ->setRequired(true);

        if ($pageName === Crud::PAGE_EDIT) {
            $roles->setPermission(EnumRoles::ROLE_ADMIN);
        }

        return [
            TextField::new('username', 'user.username')
                ->setRequired(true),
            TextField::new('password', 'user.password')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'password.new'],
                    'second_options' => ['label' => 'password.new.confirm'],
                    'constraints' => ConstraintsConfig::getPasswordConstraints(),
                    'invalid_message' => $this->translator->trans('password.not_same'),
                ])
                ->setRequired(true)
                ->onlyWhenCreating(),
            $roles,
            DateTimeField::new('derniereConnexion', 'user.derniereConnexion')->hideOnForm(),
            BooleanField::new("enable", "entity.enable"),
            TextField::new('createdBy', 'entity.createdBy')->onlyOnDetail(),
            DateTimeField::new('createdAt', 'entity.createdAt')->onlyOnDetail(),
            DateTimeField::new('updateAt', 'entity.updatedAt')->onlyOnDetail(),
            TextField::new('updatedBy', 'entity.updatedBy')->onlyOnDetail(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('user')
            ->setEntityLabelInPlural('users');
    }

    public function configureActions(Actions $actions): Actions
    {
        $changePassword = Action::new("changePassword", "user.action.changePassword")
            ->displayIf(function (?User $user) {
                return !is_null($user) && $this->securityHelper->aRolePlusGrandQue($user);
            })
            ->linkToRoute("profil_change_password", function (User $user) {
                return [
                    "user_id" => $user->getId(),
                ];
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $changePassword);
    }
}
