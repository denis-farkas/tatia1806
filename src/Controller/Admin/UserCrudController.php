<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('lastname')->setLabel('nom'),
            TextField::new('firstname')->setLabel('prénom'),
            TextField::new('email')->setLabel('email')->onlyOnIndex(),
            ChoiceField::new('roles')
            ->setLabel('Rôles')
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER',
                'Invité' => 'ROLE_GUEST',
            ])
            ->allowMultipleChoices()
            ->renderExpanded(),
            DateTimeField::new('createdAt')
                ->setFormTypeOptions([
                    'widget' => 'single_text',
                    'html5' => true,
                    'required' => false,
                ])
                ->setFormat('yyyy-MM-dd HH:mm:ss')
                ->setFormTypeOption('input', 'datetime_immutable')
                ->setLabel('Date'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW);
    }

}
