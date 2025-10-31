<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\DepartmentInterface;
use WechatWorkStaffBundle\Repository\DepartmentRepository;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\Table(name: 'wechat_work_department', options: ['comment' => '部门信息'])]
#[ORM\UniqueConstraint(name: 'wechat_work_department_idx_uniq_name', columns: ['corp_id', 'parent_id', 'name'])]
class Department implements \Stringable, DepartmentInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[TrackColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '远程ID'])]
    #[Assert\Type(type: 'integer')]
    private ?int $remoteId = null;

    #[ORM\ManyToOne(targetEntity: CorpInterface::class)]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?AgentInterface $agent = null;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '部门名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $name = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '英文名称'])]
    #[Assert\Length(max: 255)]
    private ?string $enName = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Department $parent = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['default' => '0', 'comment' => '次序值, order值大的排序靠前。有效的值范围是[0, 2^32]'])]
    #[Assert\Range(min: 0, max: 4294967296)]
    #[Assert\Length(max: 20)]
    private ?string $sortNumber = '0';

    /**
     * @var Collection<int, Department>
     */
    #[ORM\OneToMany(targetEntity: Department::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'departments', fetch: 'EXTRA_LAZY')]
    private Collection $users;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getId()})";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRemoteId(): ?int
    {
        return $this->remoteId;
    }

    public function setRemoteId(?int $remoteId): void
    {
        $this->remoteId = $remoteId;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEnName(): ?string
    {
        return $this->enName;
    }

    public function setEnName(?string $enName): void
    {
        $this->enName = $enName;
    }

    public function getSortNumber(): ?string
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?string $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    public function getParent(): ?Department
    {
        return $this->parent;
    }

    public function setParent(?Department $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Department $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
    }

    public function removeChild(Department $child): void
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
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
        }
    }

    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);
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
}
