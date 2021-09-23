<?php

namespace App\Controller\Admin;

use App\Classes\Enum\EnumRoles;
use App\Entity\User;
use App\Form\Admin\User\UserProfilForm;
use App\Form\Fields\PlainPasswordField;
use App\Repository\ParametreRepository;
use App\Repository\UserRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProfileController
 * @package App\Controller\Admin
 * @Route("/%ADMIN_PATH%")
 */
class ProfileController extends AbstractController
{
    private $passwordEncoder;
    private $em;
    private $tokenService;
    private $translator;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, TokenService $tokenService, TranslatorInterface $translator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->translator = $translator;
    }

    /**
     * @Route("/mon-profil", name="mon_profil")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $passwordForm = $this->generatePasswordForm();
        $this->handlePasswordForm($request, $passwordForm);

        $profilForm = $this->createForm(UserProfilForm::class, $this->getUser());
        $this->handleProfilForm($request, $profilForm);

        return $this->render('admin/page/user/profile.html.twig', [
            'user' => $this->getUser(),
            'passwordForm' => $passwordForm->createView(),
            'profilForm' => $profilForm->createView(),
        ]);
    }

    /**
     * @Route("/profil/change_password", name="profil_change_password")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function userChangePassword(Request $request): Response
    {
        $user_id = $request->get('user_id', null);
        if (is_null($user_id)) {
            throw new Exception("Invalid URL");
        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
        if (is_null($user)) {
            throw new Exception("Invalid URL");
        }

        $passwordForm = $this->generatePasswordForm();
        $this->handlePasswordForm($request, $passwordForm, $user_id);

        return $this->render('admin/page/user/profil_change_password.html.twig', [
            'user' => $user,
            'passwordForm' => $passwordForm->createView(),
        ]);
    }

    private function handlePasswordForm(Request $request, FormInterface $form, $user_id = null)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            /** @var User $user */
            if (is_null($user_id)) {
                $user = $this->getUser();
            } else {
                $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
            }

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $plainPassword
            ));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Mot de passe changé avec succès');
        }
    }

    private function handleProfilForm(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Votre profil a été modifié avec succès');
        }
    }

    private function generatePasswordForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('password', PlainPasswordField::class)
            ->add('send', SubmitType::class, [
                'label' => "user.action.changePassword",
                "disabled" => !$this->isGranted(EnumRoles::ROLE_ADMIN),
                "attr" => [
                    "class" => "btn btn-primary mt-3",
                ],
            ])
            ->getForm();
    }

}
