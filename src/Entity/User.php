<?php

namespace WechatWorkStaffBundle\Entity;

use AntdCpBundle\Builder\Action\ModalFormAction;
use AntdCpBundle\Service\FormFieldBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Attribute\Groups;
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
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\JsonRPC\Core\Exception\ApiException;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkStaffBundle\Message\SyncUserListMessage;
use WechatWorkStaffBundle\Repository\UserRepository;

#[AsPermission(title: '成员信息')]
#[Listable]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'wechat_work_user', options: ['comment' => '成员信息'])]
#[ORM\UniqueConstraint(name: 'wechat_work_user_idx_uniq', columns: ['user_id', 'corp_id'])]
class User implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Corp::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Corp $corp = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    private ?Agent $agent = null;

    /**
     * 对应管理端的帐号，企业内必须唯一。
     * 长度为1~64个字节。只能由数字、字母和“_-@.”四种字符组成，且第一个字符必须是数字或字母。系统进行唯一性检查时会忽略大小写。
     */
    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '成员UserID'])]
    private ?string $userId = null;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '成员名称'])]
    private ?string $name = null;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '成员别名'])]
    private ?string $alias = null;

    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '职务'])]
    private ?string $position = null;

    /**
     * 企业内必须唯一，mobile/email二者不能同时为空.
     */
    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 40, nullable: true, options: ['comment' => '手机号码'])]
    private ?string $mobile = null;

    /**
     * 长度6~64个字节，且为有效的email格式。企业内必须唯一，mobile/email二者不能同时为空.
     */
    #[Groups(['admin_curd'])]
    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '邮箱'])]
    private ?string $email = null;

    /**
     * @var Collection<Department>
     */
    #[Groups(['admin_curd'])]
    #[FormField(title: '所属部门')]
    #[Filterable(label: '所属部门')]
    #[ListColumn(title: '所属部门')]
    #[ORM\ManyToMany(targetEntity: Department::class, mappedBy: 'users', fetch: 'EXTRA_LAZY')]
    private Collection $departments;

    /**
     * 全局唯一。对于同一个服务商，不同应用获取到企业内同一个成员的open_userid是相同的，最多64个字节。仅第三方应用可获取.
     */
    #[TrackColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => '全局唯一UserID'])]
    private ?string $openUserId = null;

    #[TrackColumn]
    #[ORM\Column(length: 180, nullable: true, options: ['comment' => '头像地址'])]
    private ?string $avatarUrl = null;

    #[ORM\ManyToMany(targetEntity: UserTag::class, inversedBy: 'users', fetch: 'EXTRA_LAZY')]
    private Collection $tags;

    #[CreatedByColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[Groups(['restful_read'])]
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
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getUserId()})";
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

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

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
            $department->addUser($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            $department->removeUser($this);
        }

        return $this;
    }

    public function getOpenUserId(): ?string
    {
        return $this->openUserId;
    }

    public function setOpenUserId(?string $openUserId): self
    {
        $this->openUserId = $openUserId;

        return $this;
    }

    #[HeaderAction(title: '从企业微信服务器同步', featureKey: 'WECHAT_WORK_USER_SYNC_FROM_AGENT')]
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
                MessageBusInterface $messageBus,
                AgentRepository $agentRepository,
                LoggerInterface $logger,
            ) {
                $agent = $agentRepository->find($form['from_agent']);
                if (!$agent) {
                    throw new ApiException('找不到应用');
                }

                try {
                    $message = new SyncUserListMessage();
                    $message->setAgentId($agent->getId());
                    $messageBus->dispatch($message);
                } catch (\Throwable $exception) {
                    $logger->error('同步企微用户列表时发生异常', [
                        'exception' => $exception,
                        'agent' => $agent,
                    ]);
                    throw new ApiException('同步时发生异常：' . $exception->getMessage(), previous: $exception);
                }

                return [
                    '__message' => '正在同步，请稍后查看',
                    'form' => $form,
                    'record' => $record,
                    // 'list' => $list,
                ];
            });
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @return Collection<int, UserTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(UserTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(UserTag $tag): static
    {
        $this->tags->removeElement($tag);

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
