<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[Route('/clear-session', name: 'clear_session')]
    public function clearSession(): Response
    {
        $session = $this->requestStack->getSession();
        $session->clear(); // Clears all session data

        return new Response('Session cleared successfully.');
    }

    #[Route('/debug-session', name: 'debug_session')]
    public function debugSession(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        dump($session->all()); // Dump all session data
        return new Response('Check the debug toolbar for session data.');
    }
}