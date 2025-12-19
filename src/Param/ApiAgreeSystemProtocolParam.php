<?php

declare(strict_types=1);

namespace UserAgreementBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

final class ApiAgreeSystemProtocolParam implements RpcParamInterface
{
    #[MethodParam(description: '协议ID')]
    #[Assert\NotBlank(message: '协议ID不能为空')]
    public string $id;
}
