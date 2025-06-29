<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Child;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface):Response
    {
        $user = new User();
                $form = $this->createForm(RegisterUserType::class, $user);
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    $user = $form->getData();
                    $user->setRoles(['ROLE_GUEST']);
                    $user->setCreatedAt(new \DateTimeImmutable());
                    //figer les données
                    foreach ($form->get('children')->getData() as $child) {
                        $child->setParent($user);
                        $entityManagerInterface->persist($child);
                    }
                    $entityManagerInterface->persist($user);
                    //envoyer les données
                    $entityManagerInterface->flush();
                    $this->addFlash('success', 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.');
        
                    // Redirect to login page after successful registration
                    return $this->redirectToRoute('app_login');
                }
                return $this->render('register/index.html.twig',['form' => $form->createView()
    ]);
            }       

}
