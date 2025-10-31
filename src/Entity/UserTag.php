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
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '标签名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    private ?AgentInterface $agent = null;

    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CorpInterface $corp = null;

    #[ORM\Column(nullable: true, options: ['comment' => '企微标签ID'])]
    #[Assert\Type(type: 'integer')]
    private ?int $tagId = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tags', fetch: 'EXTRA_LAZY')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): void
    {
        $this->agent = $agent;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getTagId(): ?int
    {
        return $this->tagId;
    }

    public function setTagId(?int $tagId): void
    {
        $this->tagId = $tagId;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTag($this);
        }
    }

    public function removeUser(User $user): void
    {
        if ($this->users->removeElement($user)) {
            $user->removeTag($this);
        }
    }

    /**
     * @param Collection<int, User> $newUsers
     */
    public function replaceUsers(Collection $newUsers): void
    {
        // 先移除所有当前关联的User
        foreach ($this->users as $user) {
            $this->removeUser($user);
        }

        // 再添加新的User集合
        foreach ($newUsers as $user) {
            $this->addUser($user);
        }
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
