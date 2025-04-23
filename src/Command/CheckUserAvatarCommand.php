<?php

namespace WechatWorkStaffBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use FileSystemBundle\Service\MountManager;
use HttpClientBundle\Service\SmartHttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

#[AsCronTask('14 */8 * * *')]
#[AsCommand(name: 'wechat-work:check-user-avatar', description: '检查用户头像并保存')]
class CheckUserAvatarCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SmartHttpClient $httpClient,
        private readonly MountManager $mountManager,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = 100;

        $qb = $this->userRepository->createQueryBuilder('u')
            ->setMaxResults($limit);
        $like1 = $qb->expr()->like('u.avatarUrl', $qb->expr()->literal('https://thirdwx.qlogo.cn/%'));
        $like2 = $qb->expr()->like('u.avatarUrl', $qb->expr()->literal('https://wx.qlogo.cn/mmopen%'));
        $users = $qb->where("u.avatarUrl != '' and u.avatarUrl is not null")
            ->andWhere($like1)
            ->orWhere($like2)
            ->getQuery()
            ->toIterable();
        foreach ($users as $user) {
            /** @var User $user */
            if (empty($user->getAvatarUrl())) {
                continue;
            }

            try {
                $response = $this->httpClient->request('GET', $user->getAvatarUrl());
                $header = $response->getHeaders();
                if (!isset($header['x-errno']) && 'notexist:-6101' !== $header['x-info'][0]) {
                    $content = $response->getContent();
                    $key = $this->mountManager->saveContent($content, 'png', 'wechat-work-user');
                    $url = $this->mountManager->getImageUrl($key);
                } else {
                    $url = $_ENV['DEFAULT_USER_AVATAR_URL'];
                }

                $this->logger->info('保存企业微信用户头像', [
                    'user' => $user,
                    'new' => $url,
                ]);
                $user->setAvatarUrl($url);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } catch (\Throwable $exception) {
                $output->writeln($exception->getMessage());
                continue;
            }
        }

        return Command::SUCCESS;
    }
}
