<?php

namespace WechatWorkStaffBundle\Entity;

use AntdCpBundle\Builder\Action\ModalFormAction;
use AntdCpBundle\Service\FormFieldBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\HeaderAction;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\TreeView;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\JsonRPC\Core\Exception\ApiException;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Repository\DepartmentRepository;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;

#[AsPermission(title: '部门信息')]
#[Listable]
#[Deletable]
#[Editable]
#[Creatable]
#[TreeView(dataModel: Department::class, targetAttribute: 'parent')]
#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\Table(name: 'wechat_work_department', options: ['comment' => '部门信息'])]
#[ORM\UniqueConstraint(name: 'wechat_work_department_idx_uniq_name', columns: ['corp_id', 'parent_id', 'name'])]
class Department implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[TrackColumn]
    #[FormField]
    #[Filterable]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '远程ID'])]
    private ?int $remoteId = null;

    #[FormField(title: '所属公司')]
    #[ListColumn(title: '所属公司')]
    #[ORM\ManyToOne(targetEntity: Corp::class)]
    private ?Corp $corp = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $agent = null;

    #[IndexColumn]
    #[TrackColumn]
    #[FormField(span: 12)]
    #[Filterable]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '部门名称'])]
    private ?string $name = null;

    #[TrackColumn]
    #[FormField(span: 12)]
    #[Filterable]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '英文名称'])]
    private ?string $enName = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Department $parent = null;

    /**
     * order值大的排序靠前。有效的值范围是[0, 2^32].
     */
    #[IndexColumn]
    #[FormField]
    #[ListColumn(order: 95, sorter: true)]
    #[ORM\Column(type: Types::BIGINT, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?string $sortNumber = '0';

    /**
     * @var Collection<Department>
     */
    #[ORM\OneToMany(targetEntity: Department::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'departments', fetch: 'EXTRA_LAZY')]
    private Collection $users;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getId()})";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?int
    {
        return $this->remoteId;
    }

    public function setRemoteId(?int $remoteId): self
    {
        $this->remoteId = $remoteId;

        return $this;
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

    public function getEnName(): ?string
    {
        return $this->enName;
    }

    public function setEnName(?string $enName): self
    {
        $this->enName = $enName;

        return $this;
    }

    public function getSortNumber(): ?string
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?string $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function getParent(): ?Department
    {
        return $this->parent;
    }

    public function setParent(?Department $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Department $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Department $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    #[HeaderAction(title: '从企业微信服务器同步', featureKey: 'WECHAT_WORK_DEPARTMENT_SYNC_FROM_AGENT')]
    public function renderSyncFromAgentButton(FormFieldBuilder $fieldHelper): ModalFormAction
    {
        return ModalFormAction::gen()
            ->setFormTitle('从企业微信服务器同步')
            ->setLabel('从企业微信服务器同步')
            ->setFormWidth(600)
            ->setFormFields([
                $fieldHelper->createSelectFromEntityClass(Agent::class)
                    ->setSpan(12)
                    ->setId('from_agent')
                    ->setLabel('同步应用'),
            ])
            ->setCallback(function (
                array $form,
                array $record,
                DepartmentRepository $departmentRepository,
                EntityManagerInterface $entityManager,
                AgentRepository $agentRepository,
                WorkService $workService,
            ) {
                $agent = $agentRepository->find($form['from_agent']);
                $request = new GetDepartmentListRequest();
                $request->setAgent($agent);

                try {
                    $list = $workService->request($request);
                } catch (\Throwable $exception) {
                    throw new ApiException($exception->getMessage(), previous: $exception);
                }

                foreach ($list['department'] as $item) {
                    $local = $departmentRepository->findOneBy([
                        'corp' => $agent->getCorp(),
                        'remoteId' => $item['id'],
                    ]);
                    if (!$local) {
                        $local = new Department();
                        $local->setCorp($agent->getCorp());
                        $local->setRemoteId($item['id']);
                    }

                    $local->setAgent($agent);
                    $local->setName($item['name']);
                    $local->setSortNumber($item['order']);

                    // 保存上级
                    if ($item['parentid'] > 0) {
                        $parent = $departmentRepository->findOneBy([
                            'corp' => $agent->getCorp(),
                            'remoteId' => $item['parentid'],
                        ]);
                        if ($parent) {
                            $local->setParent($parent);
                        } else {
                            $local->setParent(null);
                        }
                    } else {
                        $local->setParent(null);
                    }

                    // TODO department_leader 未处理
                    $entityManager->persist($local);
                    $entityManager->flush();
                }

                return [
                    '__message' => '同步成功',
                    'form' => $form,
                    'record' => $record,
                    // 'list' => $list,
                ];
            });
    }

    public function getCorp(): ?Corp
    {
        return $this->corp;
    }

    public function setCorp(?Corp $corp): self
    {
        $this->corp = $corp;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
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

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
