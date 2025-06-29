<?php

namespace App\Controller;

use App\Form\ChildrenUserType;
use App\Form\PasswordUserType;
use App\Repository\UserCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {

        $user= $this->getUser();
        $form = $this->createForm(PasswordUserType::class, $user, ['passwordHasher' => $passwordHasher]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/password.html.twig', ['modifyPwdForm' => $form->createView()]);
    }
    #[Route('/compte/modifier-enfants', name: 'app_account_edit_children')]
        public function editChildren(): Response
        {
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('danger', 'Vous devez être connecté.');
                return $this->redirectToRoute('app_login');
            }

            // Les enfants sont accessibles via $user->getChildren()
            return $this->render('account/edit_children.html.twig', [
                'user' => $user,
            ]);
        } 
    
    #[Route('/compte/mes-cours', name: 'app_account_cours')]
    public function cours(): Response
    {
        $user = $this->getUser();

        // Ensure the user is authenticated
        if (!$user || !$user instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Fetch courses for the logged-in user using the getUserCours method
        $userCours = $user->getUserCours();
        dump($userCours); // Debugging line, can be removed later
        return $this->render('account/cours.html.twig', [
            'userCours' => $userCours,
        ]);
    }
}
