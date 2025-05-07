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

#[MethodTag('基础能力')]
#[MethodDoc('获取协议内容')]
#[MethodExpose('apiGetSystemProtocolContent')]
class ApiGetSystemProtocolContent extends BaseProcedure
{
    #[MethodParam('协议类型')]
    public string $type;

    public function __construct(
        private readonly Security $security,
        private readonly ProtocolEntityRepository $protocolEntityRepository,
        private readonly AgreeLogRepository $agreeLogRepository,
    ) {
    }

    public function execute(): array
    {
        if (!$this->security->getUser()) {
            throw new ApiException('未登录用户不需要同意协议');
        }

        $type = ProtocolType::tryFrom($this->type);
        if (!$type) {
            throw new ApiException('找不到指定协议类型');
        }

        $protocol = $this->protocolEntityRepository->findOneBy([
            'type' => $type,
            'valid' => true,
        ], orderBy: ['id' => 'DESC']);
        if (!$protocol) {
            throw new ApiException('找不到最新协议[1]');
        }

        $result = $protocol->retrieveApiArray();
        $result['has_agree'] = false;
        if ($this->security->getUser()) {
            $c = $this->agreeLogRepository->findOneBy([
                'protocolId' => $protocol->getId(),
                'memberId' => strval($this->security->getUser()->getId()),
            ]);
            $result['has_agree'] = $c && $c->getId() > 0;
        }

        return $result;
    }
}
