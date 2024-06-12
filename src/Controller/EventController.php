<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Security\Voter\EventVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenements')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventRepository $eventRepository,
        private readonly PaginatorInterface $paginator
    ) {
    }

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            $query = $this->eventRepository->findAll();
        } else {
            $query = $this->eventRepository->findBy(['isPublic' => true]);
        }

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('event/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/creer-un-evenement', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour créer un évènement');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $event->setOwner($this->getUser());

            $this->em->persist($event);
            $this->em->flush();

            $this->addFlash('success', 'Évènement créé avec succès');
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event): Response
    {
        if (!$this->isGranted(EventVoter::EDIT, $event)) {
            $this->addFlash('danger', "Vous n'avez pas la permission de modifier cet évènement.");
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Évènement mis à jour avec succès');
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event): Response
    {
        if (!$this->isGranted(EventVoter::DELETE, $event)) {
            $this->addFlash('danger', "Vous n'avez pas la permission de supprimer cet évènement.");
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->em->remove($event);
            $this->em->flush();
            $this->addFlash('success', 'Évènement supprimé avec succès');
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
