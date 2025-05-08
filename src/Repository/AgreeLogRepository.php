<?php

namespace UserAgreementBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use UserAgreementBundle\Entity\AgreeLog;

/**
 * @extends ServiceEntityRepository<AgreeLog>
 *
 * @method AgreeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgreeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgreeLog[]    findAll()
 * @method AgreeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgreeLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgreeLog::class);
    }
}
