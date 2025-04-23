<?php

namespace WechatWorkStaffBundle\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 创建标签（成员标签）
 *
 * @see https://developer.work.weixin.qq.com/document/path/90210
 */
class CreateTagRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名
     */
    private string $name;

    /**
     * @var int|null 标签id，非负整型，指定此参数时新增的标签会生成对应的标签id，不指定时则以目前最大的id自增
     */
    private ?int $id = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/delete';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'tagname' => $this->getName(),
        ];
        if (null !== $this->getId()) {
            $json['tagid'] = $this->getId();
        }

        return [
            'json' => $json,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
