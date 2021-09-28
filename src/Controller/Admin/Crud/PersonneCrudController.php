<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Personne;
use App\Form\Admin\ImageType;
use App\Form\AdresseType;
use App\Form\Fields\ImageTypeField;
use App\Form\NoteType;
use App\Form\RelationType;
use App\Form\TelephoneType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PersonneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Personne::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular("Personne")
            ->setEntityLabelInPlural("Personnes");
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Photo')
                ->collapsible()->renderCollapsed(true),
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

            FormField::addPanel('Relations')
                ->collapsible()->renderCollapsed(false),
            AssociationField::new("type", "Type")
                ->setColumns("col-12"),
            CollectionField::new("relations", "Relations")
                ->setEntryType(RelationType::class)
                ->setColumns("col-12"),
            CollectionField::new("notes", "Notes")
                ->setEntryType(NoteType::class)
                ->setColumns("col-12"),

            FormField::addPanel('Informations complémentaires')
                ->collapsible()->renderCollapsed(true),
            CollectionField::new("telephones", "Téléphones")
                ->setEntryType(TelephoneType::class)
                ->setColumns("col-12"),
            CollectionField::new("adresses", "Adresses")
                ->setEntryType(AdresseType::class)
                ->setColumns("col-12"),
        ];
    }
}
