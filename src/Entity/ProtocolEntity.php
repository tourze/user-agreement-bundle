<?php

namespace UserAgreementBundle\Entity;

use AntdCpBundle\Builder\Field\FileUpload;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use UserAgreementBundle\Enum\ProtocolType;
use UserAgreementBundle\Repository\ProtocolEntityRepository;

#[ORM\Entity(repositoryClass: ProtocolEntityRepository::class)]
#[ORM\Table(name: 'ims_member_protocol_entity', options: ['comment' => '协议管理'])]
class ProtocolEntity implements \Stringable, ApiArrayInterface
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[TrackColumn]
    private ?bool $valid = false;

    #[IndexColumn]
    #[ORM\Column(length: 100, enumType: ProtocolType::class, options: ['comment' => '类型'])]
    private ?ProtocolType $type = null;

    #[ORM\Column(length: 100, options: ['comment' => '协议名'])]
    private ?string $title = null;

    #[ORM\Column(length: 60, options: ['comment' => '版本'])]
    private ?string $version = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '协议内容'])]
    private ?string $content = null;

    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '条款pdf文件'])]
    private ?string $pdfUrl = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否必需'])]
    private ?bool $required = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '生效时间'])]
    private ?\DateTimeInterface $effectiveTime = null;

    public function __toString(): string
    {
        if ($this->getId() === null) {
            return '';
        }

        return "{$this->getTitle()}{$this->getVersion()}";
    }


    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getType(): ?ProtocolType
    {
        return $this->type;
    }

    public function setType(ProtocolType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getEffectiveTime(): ?\DateTimeInterface
    {
        return $this->effectiveTime;
    }

    public function setEffectiveTime(?\DateTimeInterface $effectiveTime): self
    {
        $this->effectiveTime = $effectiveTime;

        return $this;
    }

    public function getPdfUrl(): ?string
    {
        return $this->pdfUrl;
    }

    public function setPdfUrl(?string $pdfUrl): static
    {
        $this->pdfUrl = $pdfUrl;

        return $this;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'effectiveTime' => $this->getEffectiveTime()?->format('Y-m-d H:i:s'),
            'type' => $this->getType()?->value,
            'title' => $this->getTitle(),
            'version' => $this->getVersion(),
            'content' => $this->getContent(),
            'pdfUrl' => $this->getPdfUrl(),
            'required' => $this->isRequired(),
        ];
    }
}
