<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Crud\Common\BaseCrudController;
use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular("image")
            ->setEntityLabelInPlural("images");
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('url', 'image.file')
                ->setBasePath("uploads/images")
                ->hideOnForm(),
            TextField::new('imageFile', 'image.file')->setFormType(VichImageType::class)
                ->setFormTypeOption('allow_delete', false)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->onlyOnForms(),
            TextEditorField::new('description', 'image.description')
                ->setFormType(CKEditorType::class),
            IntegerField::new('image.size', 'image.size')
                ->hideOnForm()
                ->hideOnIndex(),
            TextField::new('image.mimeType', 'image.mimeType')
                ->hideOnForm()
                ->hideOnIndex(),
            TextField::new('image.originalName', 'image.originalName')
                ->hideOnForm()
                ->hideOnIndex(),
            ArrayField::new('image.dimensions', 'image.dimensions')
                ->setFormTypeOptions([
                    "allow_add" => false,
                ])
                ->hideOnForm()
                ->hideOnIndex(),
        ];
    }
}
