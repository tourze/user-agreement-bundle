<?php

namespace UserAgreementBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 注销类型
 */
enum RevokeType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case All = '1'; // 全渠道注销
    case NO_NOTIFY = '2'; // 保留用户资料注销且不同意接收品牌通知
    case NOTIFY = '3'; // 保留用户资料注销且同意接收品牌通知

    public function getLabel(): string
    {
        return match ($this) {
            self::All => '全渠道注销',
            self::NO_NOTIFY => '不接收通知',
            self::NOTIFY => '接收通知',
        };
    }
}
