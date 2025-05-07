<?php

namespace UserAgreementBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use UserAgreementBundle\Entity\RevokeRequest;

/**
 * @method RevokeRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method RevokeRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method RevokeRequest[]    findAll()
 * @method RevokeRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevokeRequestRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevokeRequest::class);
    }
}
