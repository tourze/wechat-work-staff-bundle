<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Message;

/**
 * 因为引用必然是关联 corp 的，所以这里我们没单独新增一个 corpId
 */
final class SyncUserListMessage
{
    /**
     * @var int 应用ID
     */
    private int $agentId;

    public function getAgentId(): int
    {
        return $this->agentId;
    }

    public function setAgentId(int $agentId): void
    {
        $this->agentId = $agentId;
    }
}
