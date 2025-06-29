<?php

namespace App\Controller\Admin;

use App\Entity\UserCours;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserCours::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Action::INDEX, Action::DETAIL) // Enable the "detail" action
            ->disable(Action::DELETE); // Optional: Disable delete if needed
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('cours', 'Cours')
                ->formatValue(function ($value, $entity) {
                    $cours = $entity->getCours();
                    if (!$cours) {
                        return 'No Cours Assigned';
                    }
                    return sprintf(
                        '%s (%s - %s)',
                        $cours->getName(),
                        $cours->getSchedule(),
                        $cours->getSalle()
                    );
                })
                ->onlyOnIndex(),
            TextField::new('firstname', 'User Firstname')->onlyOnIndex(),
            AssociationField::new('user', 'User')
                ->formatValue(function ($value, $entity) {
                    $user = $entity->getUser();
                    if (!$user) {
                        return 'No User Assigned';
                    }
                    return sprintf('%s (%s)', $user->getFirstname(), $user->getEmail());
                })
                ->onlyOnIndex(),
            TextEditorField::new('coursDetails', 'Cours Details')
                ->formatValue(function ($value, $entity) {
                    $cours = $entity->getCours();
                    if (!$cours) {
                        return 'No Cours Assigned';
                    }
                    $users = $cours->getUserCours();
                    $content = sprintf(
                        '<strong>%s</strong><br>Schedule: %s<br>Location: %s<br><ul>',
                        $cours->getName(),
                        $cours->getSchedule(),
                        $cours->getSalle()
                    );
                    foreach ($users as $userCours) {
                        $user = $userCours->getUser();
                        $content .= sprintf(
                            '<li>%s (%s)</li>',
                            $userCours->getFirstname(),
                            $user ? $user->getEmail() : 'No Email'
                        );
                    }
                    $content .= '</ul>';
                    return $content;
                })
                ->setTemplatePath('admin/cours_details.html.twig') // Use a custom Twig template
                ->onlyOnDetail(),
        ];
    }
}
