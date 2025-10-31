<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkStaffBundle\Repository\AgentUserRepository;

/**
 * 应用的用户信息
 *
 * 主要是有时候我们需要在应用内获取OpenID去做支付等业务，此时就需要这个表
 *
 * @see https://developer.work.weixin.qq.com/document/path/90202
 */
#[ORM\Entity(repositoryClass: AgentUserRepository::class)]
#[ORM\Table(name: 'wechat_work_agent_user', options: ['comment' => '应用用户信息'])]
class AgentUser implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?AgentInterface $agent = null;

    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '企业用户ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $userId = null;

    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => 'OpenID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private ?string $openId = null;

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

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): void
    {
        $this->openId = $openId;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
