<?php

namespace App\Form\Admin;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageType extends AbstractType
{
    private ImageRepository $imageRepository;
    private bool $canChooseInGallery = false;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("url", ImageChooserType::class, [
                "label" => false,
            ])
            ->add("imageFile", VichImageType::class, [
                "label" => "action.upload",
                "allow_delete" => false,
            ]);

        $builder
            ->addModelTransformer(new CallbackTransformer(
            //transform
                function ($value) {
                    return $value;
                },
                //untransform
                function ($value) {
                    //Si l'image a été changée (upload), on reset l'URL
                    if ($value instanceof Image && !is_null($value->getImageFile()) && $value->getImageFile()->isFile()) {
                        $value->setUrl(null);
                    }

                    //Si on a choisi une image (url) et qu'elle existe, on en recréer pas une en base de donnée
                    if ($value instanceof Image && is_null($value->getId()) && !is_null($value->getUrl())) {
                        $foundedImage = $this->imageRepository->findOneBy(["url" => $value->getUrl()]);
                        if ($foundedImage !== null) {
                            return $foundedImage;
                        }
                    }

                    return $value;
                }
            ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['canChooseInGallery'] = $this->canChooseInGallery;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Image::class,
        ]);
    }

}