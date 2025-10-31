<?php

namespace UserAgreementBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUpsertBundle\Service\UpsertManager;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

#[WithMonologChannel(channel: 'user_agreement')]
class ProtocolService
{
    public function __construct(
        private readonly ProtocolEntityRepository $protocolEntityRepository,
        private readonly AgreeLogRepository $agreeLogRepository,
        private readonly LoggerInterface $logger,
        private readonly MemberService $memberService,
        private readonly UpsertManager $upsertManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function checkAgree(UserInterface $bizUser, ProtocolType $type): bool
    {
        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], ['id' => 'DESC']);
        if (null === $protocol) {
            return false;
        }

        $agreeLog = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => $this->memberService->extractMemberId($bizUser),
        ]);
        if (null !== $agreeLog && $agreeLog->isValid()) {
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
        if (null === $protocol) {
            $this->logger->error('找不到指定的协议', [
                'type' => $type,
            ]);

            return;
        }

        $memberId = $this->memberService->extractMemberId($bizUser);

        $agreeLog = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => $memberId,
        ]);
        if (null !== $agreeLog) {
            if ($agreeLog->isValid() !== $bool) {
                $agreeLog->setValid($bool);
                $this->entityManager->persist($agreeLog);
                $this->entityManager->flush();
            }

            return;
        }

        $agreeLog = new AgreeLog();
        $agreeLog->setProtocolId((string) $protocol->getId());
        $agreeLog->setMemberId($memberId);
        $agreeLog->setValid($bool);
        $this->upsertManager->upsert($agreeLog);
    }
}
