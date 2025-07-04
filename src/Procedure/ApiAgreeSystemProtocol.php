<?php

namespace UserAgreementBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineUpsertBundle\Service\UpsertManager;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\UserIDBundle\Model\SystemUser;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Event\AgreeProtocolEvent;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

#[MethodTag(name: '基础能力')]
#[MethodDoc(summary: '同意协议')]
#[MethodExpose(method: 'apiAgreeSystemProtocol')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class ApiAgreeSystemProtocol extends LockableProcedure
{
    #[MethodParam(description: '协议ID')]
    public string $id;

    public function __construct(
        private readonly ProtocolEntityRepository $protocolEntityRepository,
        private readonly AgreeLogRepository $agreeLogRepository,
        private readonly UpsertManager $upsertManager,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(): array
    {
        $member = $this->security->getUser();

        $protocol = $this->protocolEntityRepository->findOneBy([
            'id' => $this->id,
            'valid' => true,
        ]);
        if ($protocol === null) {
            throw new ApiException('找不到协议');
        }

        // TODO: 需要根据实际 User 实体类型修改 getId() 方法调用
        $log = $this->agreeLogRepository->findOneBy([
            'memberId' => method_exists($member, 'getId') ? strval($member->getId()) : '',
            'protocolId' => $protocol->getId(),
        ]);

        if ($log === null) {
            $log = new AgreeLog();
            $log->setMemberId(method_exists($member, 'getId') ? $member->getId() : '');
            $log->setProtocolId($protocol->getId());
            $log = $this->upsertManager->upsert($log);
            assert($log instanceof AgreeLog);

            $event = new AgreeProtocolEvent();
            $event->setSender($this->security->getUser());
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
        return "ApiAgreeSystemProtocol-{$request->getParams()->get('id')}-{$this->security->getUser()->getUserIdentifier()}";
    }

    private function getSuccessResult(): array
    {
        return [
            '__message' => '已同意',
        ];
    }
}
