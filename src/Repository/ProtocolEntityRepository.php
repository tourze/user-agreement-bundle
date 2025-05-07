<?php

namespace UserAgreementBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use UserAgreementBundle\Entity\ProtocolEntity;

/**
 * @extends ServiceEntityRepository<ProtocolEntity>
 *
 * @method ProtocolEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProtocolEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProtocolEntity[]    findAll()
 * @method ProtocolEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProtocolEntityRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProtocolEntity::class);
    }
}
