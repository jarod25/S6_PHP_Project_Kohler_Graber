<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Event\Mail\ParticipantRegistrationEvent;
use App\Service\MailService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{

    public function __construct(
        private readonly StripeService $stripeService,
        private readonly EntityManagerInterface $em,
        private readonly MailService $mailService,
    )
    {
    }

    #[Route('/pay/{id}', name: 'app_pay_event')]
    public function createCheckoutSession(Event $event): Response
    {
        $checkout_session = $this->stripeService->createCheckoutSession($event, $this->generateUrl('payment_success', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL), $this->generateUrl('payment_cancel', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
        dump($checkout_session);
        return $this->redirect($checkout_session->url, 303);
    }

    #[Route('/success/{id}', name: 'payment_success')]
    public function success(Event $event): Response
    {
        $user = $this->getUser();
        $event->addParticipant($user);
        // TODO : ajouter le statut de paiement à l'entité EventParticipants


        $this->em->flush();

        $email = new ParticipantRegistrationEvent($user->getEmail());
        $email->setParams([
            'user'  => $user,
            'event' => $event,
        ]);
        $this->mailService->sendEmail($email);

        $this->addFlash('success', 'Paiement effectué avec succès ! <br> Vous êtes inscrit à l\'événement.');
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

    #[Route('/cancel/{id}', name: 'payment_cancel')]
    public function cancel(Event $event): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }
}
