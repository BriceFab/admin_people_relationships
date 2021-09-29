<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\Personne;
use App\Form\Admin\ImageType;
use App\Form\AdresseType;
use App\Form\Fields\ImageTypeField;
use App\Form\NoteType;
use App\Form\RelationType;
use App\Form\TelephoneType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PersonneCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Personne::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->showEntityActionsInlined()
            ->setEntityLabelInSingular(function (?Personne $personne) {
                return $personne ? $personne : 'Personne';
            })
            ->setEntityLabelInPlural("Personnes")
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->reorder(Crud::PAGE_DETAIL, [Action::DELETE, Action::EDIT, Action::INDEX]);
    }

    public function configureFields(string $pageName): iterable
    {
        $renderCollapse = ($pageName === Crud::PAGE_EDIT);

        return array_merge([
            FormField::addPanel('Photo')
                ->collapsible()->renderCollapsed($renderCollapse)->hideOnIndex(),
            ImageTypeField::new('photo_profil', 'Photo de profil')
                ->setFormType(ImageType::class)
                ->addCssClass("collection-images-chooser")
                ->addWebpackEncoreEntries("formImageChooser")
                ->hideOnIndex(),

            FormField::addPanel('Personne')
                ->collapsible()->renderCollapsed(false),
            TextField::new("nom", "Nom")
                ->setColumns("col-lg-6"),
            TextField::new("prenom", "Prenom")
                ->setColumns("col-lg-6"),
            ImageField::new('photo_profil', 'Photo')
                ->setBasePath('uploads/images')
                ->onlyOnIndex(),

            FormField::addPanel('Relations')
                ->collapsible()->renderCollapsed(false),
            AssociationField::new("type", "Type")
                ->setColumns("col-12")
                ->hideOnIndex(),
            CollectionField::new("relations", "Relations")
                ->setEntryType(RelationType::class)
                ->setTemplatePath('admin/fields/relations.html.twig')
                ->setColumns("col-12"),
            CollectionField::new("notes", "Notes")
                ->setEntryType(NoteType::class)
                ->setColumns("col-12")
                ->hideOnIndex(),

            FormField::addPanel('Informations complémentaires')
                ->collapsible()->renderCollapsed($renderCollapse),
            CollectionField::new("telephones", "Téléphones")
                ->setEntryType(TelephoneType::class)
                ->setColumns("col-12")
                ->hideOnIndex(),
            CollectionField::new("adresses", "Adresses")
                ->setEntryType(AdresseType::class)
                ->setColumns("col-12")
                ->hideOnIndex(),
        ], $this->getBlameableTimestampableFields());
    }
}
