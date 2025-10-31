<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkStaffBundle\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'wechat_work_user', options: ['comment' => '成员信息'])]
#[ORM\UniqueConstraint(name: 'wechat_work_user_idx_uniq', columns: ['user_id', 'corp_id'])]
class User implements \Stringable, UserInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    private ?AgentInterface $agent = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '成员UserID，对应管理端的帐号，企业内必须唯一。长度为1~64个字节。只能由数字、字母和_-@.四种字符组成，且第一个字符必须是数字或字母'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 64)]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9][a-zA-Z0-9_\-@.]*$/')]
    private ?string $userId = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '成员名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private ?string $name = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '成员别名'])]
    #[Assert\Length(max: 255)]
    private ?string $alias = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '职务'])]
    #[Assert\Length(max: 64)]
    private ?string $position = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: true, options: ['comment' => '手机号码，企业内必须唯一，mobile/email二者不能同时为空'])]
    #[Assert\Length(max: 40)]
    #[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: '手机号码格式不正确')]
    private ?string $mobile = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '邮箱，长度6~64个字节，且为有效的email格式。企业内必须唯一，mobile/email二者不能同时为空'])]
    #[Assert\Email]
    #[Assert\Length(min: 6, max: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, Department>
     */
    #[ORM\ManyToMany(targetEntity: Department::class, mappedBy: 'users', fetch: 'EXTRA_LAZY')]
    private Collection $departments;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => '全局唯一UserID，对于同一个服务商，不同应用获取到企业内同一个成员的open_userid是相同的，最多64个字节'])]
    #[Assert\Length(max: 120)]
    private ?string $openUserId = null;

    #[TrackColumn]
    #[ORM\Column(length: 180, nullable: true, options: ['comment' => '头像地址'])]
    #[Assert\Url]
    #[Assert\Length(max: 180)]
    private ?string $avatarUrl = null;

    /**
     * @var Collection<int, UserTag>
     */
    #[ORM\ManyToMany(targetEntity: UserTag::class, inversedBy: 'users', fetch: 'EXTRA_LAZY')]
    private Collection $tags;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getUserId()})";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): void
    {
        $this->agent = $agent;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): void
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->addUser($this);
        }
    }

    public function removeDepartment(Department $department): void
    {
        if ($this->departments->removeElement($department)) {
            $department->removeUser($this);
        }
    }

    public function getOpenUserId(): ?string
    {
        return $this->openUserId;
    }

    public function setOpenUserId(?string $openUserId): void
    {
        $this->openUserId = $openUserId;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return Collection<int, UserTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(UserTag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(UserTag $tag): void
    {
        $this->tags->removeElement($tag);
    }
}
