<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAvailableEvents() {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isPublic = :isPublic')
            ->andWhere('e.nbMaxParticipants > :nbMaxParticipants')
            ->setParameter('isPublic', 1)
            ->setParameter('nbMaxParticipants', 1)
            ->getQuery()
            ->getResult();
    }

    public function findPublicOrOwnedEvents(?UserInterface $user)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.isPublic = :public')
            ->setParameter('public', true);

        if ($user) {
            $qb->orWhere('e.owner = :owner')
                ->setParameter('owner', $user);
        }

        return $qb->getQuery();
    }
}
