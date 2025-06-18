<?php

namespace UserAgreementBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use UserAgreementBundle\Repository\AgreeLogRepository;

#[ORM\Table(name: 'ims_member_protocol_agree_log', options: ['comment' => '接受协议日志'])]
#[ORM\Entity(repositoryClass: AgreeLogRepository::class)]
#[ORM\UniqueConstraint(name: 'member_protocol_agree_log_idx_uniq', columns: ['protocol_id', 'member_id'])]
class AgreeLog
{
    use TimestampableAware;
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[IndexColumn]
    #[ORM\Column(length: 64)]
    private ?string $protocolId = null;

    #[IndexColumn]
    #[ORM\Column(length: 64)]
    private ?string $memberId = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['comment' => '同意', 'default' => 1])]
    private bool $valid = true;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProtocolId(): ?string
    {
        return $this->protocolId;
    }

    public function setProtocolId(string $protocolId): self
    {
        $this->protocolId = $protocolId;

        return $this;
    }

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function setMemberId(string $memberId): self
    {
        $this->memberId = $memberId;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }}
