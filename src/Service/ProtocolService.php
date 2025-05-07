<?php

namespace UserAgreementBundle\Service;

use AppBundle\Entity\BizUser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tourze\Symfony\Async\Attribute\Async;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

class ProtocolService
{
    public function __construct(
        private readonly ProtocolEntityRepository $protocolEntityRepository,
        private readonly AgreeLogRepository $agreeLogRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function checkAgree(BizUser $bizUser, ProtocolType $type): bool
    {
        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], orderBy: ['id' => 'DESC']);
        if (!$protocol) {
            return false;
        }

        $agreeLog = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => strval($bizUser->getId()),
        ]);
        if ($agreeLog && $agreeLog->isValid()) {
            return true;
        }

        return false;
    }

    #[Async]
    public function autoAgree(BizUser $bizUser, ProtocolType $type, bool $bool = true): void
    {
        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], orderBy: ['id' => 'DESC']);
        if (!$protocol) {
            $this->logger->error('找不到指定的协议', [
                'type' => $type,
            ]);

            return;
        }

        $agreeLog = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => strval($bizUser->getId()),
        ]);
        if ($agreeLog) {
            if ($agreeLog->isValid() !== $bool) {
                $agreeLog->setValid($bool);
                $this->entityManager->persist($agreeLog);
                $this->entityManager->flush();
            }

            return;
        }

        $agreeLog = new AgreeLog();
        $agreeLog->setProtocolId($protocol->getId());
        $agreeLog->setMemberId($bizUser->getId());
        $agreeLog->setValid($bool);
        $this->entityManager->persist($agreeLog);
        $this->entityManager->flush();
    }
}
