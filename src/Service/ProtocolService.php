<?php

namespace UserAgreementBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;
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

    public function checkAgree(UserInterface $bizUser, ProtocolType $type): bool
    {
        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], ['id' => 'DESC']);
        if ($protocol === null) {
            return false;
        }

        // TODO: 需要根据实际 User 实体类型修改 getId() 方法调用
        $agreeLog = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => method_exists($bizUser, 'getId') ? strval($bizUser->getId()) : '',
        ]);
        if ($agreeLog !== null && $agreeLog->isValid()) {
            return true;
        }

        return false;
    }

    #[Async]
    public function autoAgree(UserInterface $bizUser, ProtocolType $type, bool $bool = true): void
    {
        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], ['id' => 'DESC']);
        if ($protocol === null) {
            $this->logger->error('找不到指定的协议', [
                'type' => $type,
            ]);

            return;
        }

        $agreeLog = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => method_exists($bizUser, 'getId') ? strval($bizUser->getId()) : '',
        ]);
        if ($agreeLog !== null) {
            if ($agreeLog->isValid() !== $bool) {
                $agreeLog->setValid($bool);
                $this->entityManager->persist($agreeLog);
                $this->entityManager->flush();
            }

            return;
        }

        $agreeLog = new AgreeLog();
        $agreeLog->setProtocolId($protocol->getId());
        $agreeLog->setMemberId(method_exists($bizUser, 'getId') ? $bizUser->getId() : '');
        $agreeLog->setValid($bool);
        $this->entityManager->persist($agreeLog);
        $this->entityManager->flush();
    }
}
