<?php

namespace UserAgreementBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Repository\AgreeLogRepository;
use UserAgreementBundle\Repository\ProtocolEntityRepository;
use UserAgreementBundle\Service\MemberService;

#[MethodTag(name: '用户协议')]
#[MethodDoc(summary: '获取协议内容')]
#[MethodExpose(method: 'apiGetSystemProtocolContent')]
class ApiGetSystemProtocolContent extends BaseProcedure
{
    #[MethodParam(description: '协议类型')]
    public string $type;

    public function __construct(
        private readonly Security $security,
        private readonly ProtocolEntityRepository $protocolEntityRepository,
        private readonly AgreeLogRepository $agreeLogRepository,
        private readonly MemberService $memberService,
    ) {
    }

    public function execute(): array
    {
        if (null === $this->security->getUser()) {
            throw new ApiException('未登录用户不需要同意协议');
        }

        $type = ProtocolType::tryFrom($this->type);
        if (null === $type) {
            throw new ApiException('找不到指定协议类型');
        }

        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], ['id' => 'DESC']);
        if (null === $protocol) {
            throw new ApiException('找不到最新协议[1]');
        }

        $result = $protocol->retrieveApiArray();
        $user = $this->security->getUser();
        $c = $this->agreeLogRepository->findOneBy([
            'protocolId' => $protocol->getId(),
            'memberId' => $this->memberService->extractMemberId($user),
        ]);
        $result['has_agree'] = null !== $c && $c->getId() > 0;

        return $result;
    }
}
