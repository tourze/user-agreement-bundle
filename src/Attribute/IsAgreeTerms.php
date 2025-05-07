<?php

namespace UserAgreementBundle\Attribute;

use UserAgreementBundle\Enum\ProtocolType;

/**
 * 用于声明这个类访问时必须已统一了指定协议
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class IsAgreeTerms
{
    public function __construct(
        public ProtocolType $type,
    ) {
    }
}
