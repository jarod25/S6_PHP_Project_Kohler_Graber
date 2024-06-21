<?php

namespace App\Entity;

use App\Repository\EventParticipantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventParticipantsRepository::class)]
#[ORM\Table(name: 'events__participants')]
class EventParticipants
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'participantsEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentStatus = null;

    #[ORM\Column]
    private ?bool $hasPaid = null;

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function isHasPaid(): ?bool
    {
        return $this->hasPaid;
    }

    public function setHasPaid(bool $hasPaid): static
    {
        $this->hasPaid = $hasPaid;

        return $this;
    }
}
