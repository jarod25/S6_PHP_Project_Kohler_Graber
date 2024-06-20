<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Security\Voter\EventVoter;
use App\Service\AvailablePlacesService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(
        private readonly EventRepository        $eventRepository,
        private readonly AvailablePlacesService $availablePlacesService,
        private readonly PaginatorInterface     $paginator,
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

        foreach ($query as $event) {
            $event->availablePlaces = $this->availablePlacesService->calculateAvailablePlaces($event);
        }

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('home/index.html.twig', [
            'events' => $pagination,
        ]);
    }

    #[Route('/mes-evenements', name: 'app_event_my_events', methods: ['GET', 'POST'])]
    public function myEvents(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour voir vos événements');
            return $this->redirectToRoute('app_login');
        }

        $events = $user->getParticipantsEvents();
        foreach ($events as $event) {
            $event->availablePlaces = $this->availablePlacesService->calculateAvailablePlaces($event);
        }

        $pagination = $this->paginator->paginate(
            $events->toArray(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('event/my_events.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
