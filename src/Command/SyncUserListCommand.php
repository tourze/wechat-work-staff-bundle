<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatWorkStaffBundle\Message\SyncUserListMessage;
use WechatWorkStaffBundle\MessageHandler\SyncUserListHandler;

#[AsCommand(name: self::NAME, description: '同步指定企业的用户列表')]
class SyncUserListCommand extends Command
{
    public const NAME = 'wechat-work:sync-user-list';

    public function __construct(private readonly SyncUserListHandler $handler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // 通过应用ID能定位企业，所以只需要一个参数
        $this->addArgument('agentId', InputArgument::REQUIRED, '应用ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $agentId = $input->getArgument('agentId');
        assert(is_int($agentId) || (is_string($agentId) && ctype_digit($agentId)));
        $agentIdInt = is_int($agentId) ? $agentId : (int) $agentId;

        $message = new SyncUserListMessage();
        $message->setAgentId($agentIdInt);
        $this->handler->__invoke($message);

        return Command::SUCCESS;
    }
}
