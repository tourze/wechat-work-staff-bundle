<?php

namespace WechatWorkStaffBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkStaffBundle\Repository\UserTagRepository;

/**
 * @see https://developer.work.weixin.qq.com/document/path/90210
 */
#[ORM\Entity(repositoryClass: UserTagRepository::class)]
#[ORM\Table(name: 'wechat_work_tag', options: ['comment' => '成员标签'])]
#[ORM\UniqueConstraint(name: 'wechat_work_tag_idx_uniq', columns: ['corp_id', 'tag_id'])]
class UserTag implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标签名'])]
    private ?string $name = null;

    #[ORM\ManyToOne]
    private ?AgentInterface $agent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\Column(nullable: true, options: ['comment' => '企微标签ID'])]
    private ?int $tagId = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tags', fetch: 'EXTRA_LAZY')]
    private Collection $users;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): static
    {
        $this->corp = $corp;

        return $this;
    }

    public function getTagId(): ?int
    {
        return $this->tagId;
    }

    public function setTagId(?int $tagId): static
    {
        $this->tagId = $tagId;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTag($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeTag($this);
        }

        return $this;
    }

    public function replaceUsers(Collection $newUsers): static
    {
        // 先移除所有当前关联的User
        foreach ($this->users as $user) {
            $this->removeUser($user);
        }

        // 再添加新的User集合
        foreach ($newUsers as $user) {
            $this->addUser($user);
        }

        return $this;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }
    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
