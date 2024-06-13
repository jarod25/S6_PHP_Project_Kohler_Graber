<?php

namespace App\Controller;

use App\Form\User\AccountType;
use App\Form\User\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository
    )
    {
    }

    #[Route('/profil', name: 'app_profile')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        {
            $user = $this->getUser();

            if (!$user) {
                return $this->redirectToRoute('app_login');
            }

            $email = $user->getEmail();

            $form_infos = $this->createForm(AccountType::class, $user);
            $form_infos->handleRequest($request);

            $form_pwd = $this->createForm(ChangePasswordType::class);
            $form_pwd->handleRequest($request);

            if ($form_infos->isSubmitted() && $form_infos->isValid()) {
                if ($form_infos->get('email')->getData() !== $email) {
                    $userExist = $this->userRepository->findOneBy(['email' => $form_infos->get('email')->getData()]);
                    if ($userExist) {
                        $form_infos->get('email')->addError(new FormError('Cet email est déjà utilisé sur un autre compte.'));
                        return $this->render('user/profile.html.twig', [
                            'form_infos' => $form_infos->createView(),
                            'form_pwd' => $form_pwd->createView(),
                        ]);
                    }
                }

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', 'Vos informations ont bien été mises à jour.');
                return $this->redirectToRoute('app_profile');
            }

            if ($form_pwd->isSubmitted() && $form_pwd->isValid()) {
                $oldPassword = $form_pwd->get('oldPassword')->getData();
                if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                    $form_pwd->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));
                    return $this->render('user/profile.html.twig', [
                        'form_infos' => $form_infos->createView(),
                        'form_pwd' => $form_pwd->createView(),
                    ]);
                }

                if ($form_pwd->get('oldPassword')->getData() === $form_pwd->get('password')->getData()) {
                    $form_pwd->get('password')->addError(new FormError('Le nouveau mot de passe doit être différent de l\'ancien.'));
                    return $this->render('user/profile.html.twig', [
                        'form_infos' => $form_infos->createView(),
                        'form_pwd' => $form_pwd->createView(),
                    ]);
                }

                if ($form_pwd->get('password')->getData()) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form_pwd->get('password')->getData()
                        )
                    );
                }
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été mis à jour.');
                return $this->redirectToRoute('app_profile');
            }
            return $this->render('user/profile.html.twig', [
                'form_infos' => $form_infos->createView(),
                'form_pwd' => $form_pwd->createView(),
            ]);
        }
    }
}
