<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\Traduction;
use App\Repository\LangueRepository;
use Doctrine\ORM\Query;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TraductionCrudController extends BaseCrudController
{
    private array $langs_choice = [];

    public function __construct(LangueRepository $langRepo)
    {
        $langs = $langRepo->createQueryBuilder("l")
            ->select("l")
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        foreach ($langs as $lang) {
            $this->langs_choice[$lang["langue"]] = $lang["code"];
        }
    }

    public static function getEntityFqcn(): string
    {
        return Traduction::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('traduction')
            ->setEntityLabelInPlural('traductions')
            ->overrideTemplate('crud/edit', 'admin/fields/traduction.edit.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('key', "traduction.key")
                ->setFormTypeOption('disabled', $pageName !== Crud::PAGE_NEW ?? 'disabled'),
            TextField::new('domain', "traduction.domain")
                ->setFormTypeOption('disabled', 'disabled')->hideOnIndex(),
            ChoiceField::new('locale', "traduction.locale")
                ->setChoices($this->langs_choice)
                ->allowMultipleChoices(false)
                ->setRequired(true),
            TextareaField::new('translation', "traduction.translation")
                ->setFormType($pageName !== Crud::PAGE_NEW ? CKEditorType::class : TextareaType::class),
            TextField::new('createdBy', 'entity.createdBy')->onlyOnDetail(),
            DateTimeField::new('createdAt', 'entity.createdAt')->onlyOnDetail(),
            DateTimeField::new('updateAt', 'entity.updatedAt')->onlyOnDetail(),
            TextField::new('updatedBy', 'entity.updatedBy')->onlyOnDetail(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(TextFilter::new("key", "traduction.key"))
            ->add(ChoiceFilter::new("locale", "traduction.locale")->setChoices($this->langs_choice))
            ->add(TextFilter::new("translation", "traduction.translation"))
            ->add(TextFilter::new("createdBy", "entity.createdBy"));
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, self::actionClearCache());
    }

    private function actionClearCache(): Action
    {
        return Action::new("clearCache", "action.clear.cache")
            ->setIcon("fa fa-refresh")
            ->linkToRoute("admin_clear_trad_cache")
            ->createAsGlobalAction()
            ->addCssClass("btn btn-secondary");
    }
}
