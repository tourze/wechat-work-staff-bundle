<?php

namespace WechatWorkStaffBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatWorkStaffBundle\Message\SyncUserListMessage;
use WechatWorkStaffBundle\MessageHandler\SyncUserListHandler;

#[AsCommand(name: 'wechat-work:sync-user-list', description: '同步指定企业的用户列表')]
class SyncUserListCommand extends Command
{
    public const NAME = 'sync-user-list';

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
        $message = new SyncUserListMessage();
        $message->setAgentId($input->getArgument('agentId'));
        $this->handler->__invoke($message);

        return Command::SUCCESS;
    }
}
