<?php

namespace UserAgreementBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use UserAgreementBundle\Enum\RevokeType;
use UserAgreementBundle\Repository\RevokeRequestRepository;

#[ORM\Table(name: 'crm_revoke_request', options: ['comment' => '用户注销记录'])]
#[ORM\Entity(repositoryClass: RevokeRequestRepository::class)]
class RevokeRequest implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;
    use CreatedFromIpAware;

    #[Groups(groups: ['restful_read'])]
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[ORM\Column(length: 10, enumType: RevokeType::class, options: ['comment' => '注销类型'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [RevokeType::class, 'cases'])]
    private ?RevokeType $type = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['comment' => '身份信息'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $identity = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '头像'])]
    #[Assert\Length(max: 255)]
    private ?string $avatar = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '昵称'])]
    #[Assert\Length(max: 255)]
    private ?string $nickName = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '备注信息'])]
    #[Assert\Length(max: 100)]
    private ?string $remark = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private ?int $lockVersion = null;

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getType(): ?RevokeType
    {
        return $this->type;
    }

    public function setType(?RevokeType $type): void
    {
        $this->type = $type;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    public function setIdentity(string $identity): void
    {
        $this->identity = $identity;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(?string $nickName): void
    {
        $this->nickName = $nickName;
    }

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): void
    {
        $this->lockVersion = $lockVersion;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
