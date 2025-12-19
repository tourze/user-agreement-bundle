<?php

declare(strict_types=1);

namespace UserAgreementBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

final class ApiGetSystemProtocolContentParam implements RpcParamInterface
{
    #[MethodParam(description: '协议类型')]
    #[Assert\NotBlank(message: '协议类型不能为空')]
    public string $type;
}
