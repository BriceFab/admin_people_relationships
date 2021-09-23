<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Personne;
use App\Form\AdresseType;
use App\Form\NoteType;
use App\Form\RelationType;
use App\Form\TelephoneType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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
            TextField::new("nom", "Nom")
                ->setColumns("col-lg-6"),
            TextField::new("prenom", "Prenom")
                ->setColumns("col-lg-6"),
            AssociationField::new("type", "Type")
                ->setColumns("col-12"),
            CollectionField::new("telephones", "Téléphones")
                ->setEntryType(TelephoneType::class)
                ->setColumns("col-12"),
            CollectionField::new("adresses", "Adresses")
                ->setEntryType(AdresseType::class)
                ->setColumns("col-12"),
            CollectionField::new("relations", "Relations")
                ->setEntryType(RelationType::class)
                ->setColumns("col-12"),
            CollectionField::new("notes", "Notes")
                ->setEntryType(NoteType::class)
                ->setColumns("col-12"),
        ];
    }
}
