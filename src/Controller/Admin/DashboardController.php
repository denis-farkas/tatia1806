<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use App\Entity\Cours;
use App\Entity\Event;
use App\Entity\Outfit;
use App\Entity\Product;
use App\Entity\Professor;
use App\Entity\Sponsor;
use App\Entity\User;
use App\Entity\Gala;
use App\Entity\Order;
use App\Entity\UserCours;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        ->setTitle('<a href="' . $this->generateUrl('app_home') . '">Ecole de danse Sara Leclerc</a>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Cours', 'fas fa-list', Cours::class);
        yield MenuItem::linkToCrud('Utilisateurs Cours', 'fas fa-list', UserCours::class);
        yield MenuItem::linkToCrud('Professeurs', 'fas fa-list', Professor::class);
        yield MenuItem::linkToCrud('Sponsors', 'fas fa-list', Sponsor::class);
        yield MenuItem::linkToCrud('Outfit', 'fas fa-list', Outfit::class);
        yield MenuItem::linkToCrud('Attributs', 'fas fa-tags', Attribute::class);
        yield MenuItem::linkToCrud('Product', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Event', 'fas fa-list', Event::class);
        yield MenuItem::linkToCrud('Gala', 'fas fa-list', Gala::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-list', Order::class);
    }
}
