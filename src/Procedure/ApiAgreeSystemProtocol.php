<?php

namespace UserAgreementBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\UserIDBundle\Model\SystemUser;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Event\AgreeProtocolEvent;
use UserAgreementBundle\Param\ApiAgreeSystemProtocolParam;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;
use UserAgreementBundle\Service\MemberService;

#[MethodTag(name: '用户协议')]
#[MethodDoc(summary: '同意协议')]
#[MethodExpose(method: 'apiAgreeSystemProtocol')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
final class ApiAgreeSystemProtocol extends LockableProcedure
{
    public function __construct(
        private readonly ProtocolEntityRepository $protocolEntityRepository,
        private readonly AgreeLogRepository $agreeLogRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly MemberService $memberService,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @phpstan-param ApiAgreeSystemProtocolParam $param
     */
    public function execute(ApiAgreeSystemProtocolParam|RpcParamInterface $param): ArrayResult
    {
        $member = $this->security->getUser();
        if (null === $member) {
            throw new ApiException('用户未登录');
        }

        $protocol = $this->protocolEntityRepository->findOneBy([
            'id' => $param->id,
            'valid' => true,
        ]);
        if (null === $protocol) {
            throw new ApiException('找不到协议');
        }

        $mId = $this->memberService->extractMemberId($member);

        $log = $this->agreeLogRepository->findOneBy([
            'memberId' => $mId,
            'protocolId' => $protocol->getId(),
        ]);

        if (null === $log) {
            $log = new AgreeLog();
            $log->setMemberId($mId);
            $log->setProtocolId((string) $protocol->getId());
            $this->entityManager->persist($log);
            $this->entityManager->flush();

            $event = new AgreeProtocolEvent();
            $sender = $this->security->getUser();
            if (null !== $sender) {
                $event->setSender($sender);
            }
            $event->setReceiver(SystemUser::instance());
            $event->setMessage(sprintf('同意《%s》协议', $protocol));
            $event->setProtocol($protocol);
            $event->setAgreeLog($log);
            $this->eventDispatcher->dispatch($event);
        }

        return $this->getSuccessResult();
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): ?string
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return null;
        }

        $params = $request->getParams();
        if (null === $params) {
            return null;
        }

        $id = $params->get('id');
        assert(is_string($id) || is_int($id), 'Protocol ID must be string or int');
        $protocolId = (string) $id;

        return "ApiAgreeSystemProtocol-{$protocolId}-{$user->getUserIdentifier()}";
    }

    /**
     * @return array<string, string>
     */
    private function getSuccessResult(): array
    {
        return new ArrayResult([
            '__message' => '已同意',
        ]);
    }
}
