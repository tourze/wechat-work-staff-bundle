<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 更新成员
 *
 * @see https://developer.work.weixin.qq.com/document/path/90197
 */
class CreateUserRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
     */
    private string $userId;

    /**
     * @var string 成员名称。长度为1~64个utf8字符
     */
    private string $name;

    /**
     * @var string|null 别名。长度为1-64个utf8字符
     */
    private ?string $alias;

    /**
     * @var string|null 手机号码。企业内必须唯一。若成员已激活企业微信，则需成员自行修改（此情况下该参数被忽略，但不会报错）
     */
    private ?string $mobile;

    /**
     * @var string|null 职务信息。长度为0~128个字符
     */
    private ?string $position;

    /**
     * @var int|null 性别。1表示男性，2表示女性
     */
    private ?int $gender;

    /**
     * @var string|null 邮箱。长度不超过64个字节，且为有效的email格式。企业内必须唯一。若是绑定了腾讯企业邮箱的企业微信，则需要在腾讯企业邮箱中修改邮箱（此情况下该参数被忽略，但不会报错）
     */
    private ?string $email;

    /**
     * @var array<int, int> 成员所属部门id列表，不超过100个
     */
    private array $department;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/create';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [
            'userid' => $this->getUserId(),
            'name' => $this->getName(),
            'department' => $this->getDepartment(),
        ];

        if (null !== $this->getAlias()) {
            $json['alias'] = $this->getAlias();
        }
        if (null !== $this->getMobile()) {
            $json['mobile'] = $this->getMobile();
        }
        if (null !== $this->getPosition()) {
            $json['position'] = $this->getPosition();
        }
        if (null !== $this->getGender()) {
            $json['gender'] = $this->getGender();
        }
        if (null !== $this->getEmail()) {
            $json['email'] = $this->getEmail();
        }

        return [
            'json' => $json,
        ];
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): void
    {
        $this->gender = $gender;
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
     * @return array<int, int>
     */
    public function getDepartment(): array
    {
        return $this->department;
    }

    /**
     * @param array<int, int> $department
     */
    public function setDepartment(array $department): void
    {
        $this->department = $department;
    }
}
