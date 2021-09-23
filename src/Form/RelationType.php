<?php

namespace App\Form;

use App\Controller\Admin\Crud\PersonneCrudController;
use App\Entity\Personne;
use App\Entity\Relation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationType extends AbstractType
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $crudControllerFqcn = $currentRequest->get('crudControllerFqcn');
        $entityId = $currentRequest->get('entityId');

        $personneQueryBuilder = null;
        if (!empty($crudControllerFqcn) && !empty($entityId) && $crudControllerFqcn === PersonneCrudController::class) {
            //On exclu la current personne dans l'ajout d'une relation
            $personneQueryBuilder = function (EntityRepository $repo) use ($entityId) {
                return $repo->createQueryBuilder("e")
                    ->andWhere("e.id != :id")
                    ->setParameter("id", $entityId);
            };
        }

        $builder
            ->add("type", EntityType::class, [
                "label" => "Type",
                "class" => \App\Entity\RelationType::class,
            ])
            ->add("personne", EntityType::class, [
                "label" => "Personne",
                "class" => Personne::class,
                "query_builder" => $personneQueryBuilder,
            ])
            ->add("notes", CollectionType::class, [
                "label" => "Notes",
                "entry_type" => NoteType::class,
                "allow_add" => true,
                "allow_delete" => true,
                "entry_options" => [
                    "label" => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Relation::class,
        ]);
    }

}