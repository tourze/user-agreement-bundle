<?php

namespace UserAgreementBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use UserAgreementBundle\Repository\AgreeLogRepository;

#[ORM\Table(name: 'ims_member_protocol_agree_log', options: ['comment' => '接受协议日志'])]
#[ORM\Entity(repositoryClass: AgreeLogRepository::class)]
#[ORM\UniqueConstraint(name: 'member_protocol_agree_log_idx_uniq', columns: ['protocol_id', 'member_id'])]
class AgreeLog implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['comment' => '协议ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $protocolId = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['comment' => '成员ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $memberId = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['comment' => '同意', 'default' => 1])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    private bool $valid = true;

    public function getProtocolId(): ?string
    {
        return $this->protocolId;
    }

    public function setProtocolId(string $protocolId): void
    {
        $this->protocolId = $protocolId;
    }

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function setMemberId(string $memberId): void
    {
        $this->memberId = $memberId;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function __toString(): string
    {
        // 确保返回非空字符串，即使 ID 为 null
        return $this->getId() ?? '';
    }
}
