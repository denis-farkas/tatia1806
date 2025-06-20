<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChildrenController extends AbstractController
{
    #[Route('/compte/ajouter-enfant', name: 'app_create_children', methods: ['POST'])]
    public function createChild(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('account_edit_children');
        }

        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $birthdate = new \DateTime($request->request->get('birthdate'));

        // Vérification doublon
        $existing = $em->getRepository(Child::class)->findOneBy([
            'parent' => $user,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'birthdate' => $birthdate,
        ]);
        if ($existing) {
            $this->addFlash('warning', 'Cet enfant est déjà déclaré.');
            return $this->redirectToRoute('app_account_edit_children');
        }

        $child = new Child();
        $child->setFirstname($firstname);
        $child->setLastname($lastname);
        $child->setBirthdate($birthdate);
        $child->setParent($user);

        $em->persist($child);
        $em->flush();

        $this->addFlash('success', 'Enfant ajouté avec succès.');
        return $this->redirectToRoute('app_account_edit_children');
    }

    #[Route('/compte/modifier-enfant', name: 'app_edit_children', methods: ['POST'])]
    public function editChild(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $childId = $request->request->get('child_id');
        $child = $em->getRepository(Child::class)->find($childId);

        if (!$child || $child->getParent() !== $user) {
            $this->addFlash('danger', 'Enfant non trouvé ou non autorisé.');
            return $this->redirectToRoute('app_account');
        }

        $child->setFirstname($request->request->get('firstname'));
        $child->setLastname($request->request->get('lastname'));
        $child->setBirthdate(new \DateTime($request->request->get('birthdate')));

        $em->flush();

        $this->addFlash('success', 'Enfant modifié avec succès.');
        return $this->redirectToRoute('app_account_edit_children');
    }

    #[Route('/compte/supprimer-enfant/{id}', name: 'app_delete_children', methods: ['POST'])]
    public function deleteChild(int $id, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $child = $em->getRepository(Child::class)->find($id);

        if (!$child || $child->getParent() !== $user) {
            $this->addFlash('danger', 'Enfant non trouvé ou non autorisé.');
            return $this->redirectToRoute('app_account');
        }

        $em->remove($child);
        $em->flush();

        $this->addFlash('success', 'Enfant supprimé avec succès.');
        return $this->redirectToRoute('app_account_edit_children');
    }
}