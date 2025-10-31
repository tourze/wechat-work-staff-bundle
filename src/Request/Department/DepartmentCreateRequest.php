<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\Department;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 创建部门
 *
 * @see https://developer.work.weixin.qq.com/document/path/90205
 */
class DepartmentCreateRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var int|null 部门id
     */
    private ?int $id = null;

    /**
     * @var string 部门名称。长度限制为1~32个字符，字符不能包括:*?"<>｜
     */
    private string $name;

    /**
     * @var string|null 英文名称，需要在管理后台开启多语言支持才能生效。长度限制为1~32个字符，字符不能包括:*?"<>｜
     */
    private ?string $enName = null;

    /**
     * @var int 父部门id
     */
    private int $parentId;

    /**
     * @var int|null 在父部门中的次序值。order值大的排序靠前。有效的值范围是[0, 2^32)
     */
    private ?int $order = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/department/create';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [
            'name' => $this->getName(),
            'parentid' => $this->getParentId(),
        ];

        if (null !== $this->getEnName()) {
            $json['name_en'] = $this->getEnName();
        }

        if (null !== $this->getOrder()) {
            $json['order'] = $this->getOrder();
        }

        if (null !== $this->getId()) {
            $json['id'] = $this->getId();
        }

        return [
            'json' => $json,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }
}
