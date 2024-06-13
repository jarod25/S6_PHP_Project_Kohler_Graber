<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(
        private readonly EventRepository    $eventRepository,
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            $query = $this->eventRepository->findAll();
        } else {
            $query = $this->eventRepository->findAvailableEvents();
        }

        return $this->render('home/index.html.twig', [
            'events' => $query,
        ]);
    }
}
