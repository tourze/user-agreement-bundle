<?php

namespace UserAgreementBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 协议类型
 */
enum ProtocolType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case MEMBER_REGISTER = 'member_register';
    case MEMBER_USAGE = 'member_usage';
    case PRIVACY = 'privacy';
    case SALE_PUSH = 'sale-push';

    public function getLabel(): string
    {
        return match ($this) {
            self::MEMBER_REGISTER => '用户注册协议',
            self::MEMBER_USAGE => '用户使用协议',
            self::PRIVACY => '隐私协议',
            self::SALE_PUSH => '营销推送',
        };
    }
}
