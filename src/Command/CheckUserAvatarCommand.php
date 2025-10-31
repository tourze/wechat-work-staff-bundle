<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

#[AsCronTask(expression: '14 */8 * * *')]
#[AsCommand(name: self::NAME, description: '检查用户头像并保存')]
#[Autoconfigure(public: true)]
class CheckUserAvatarCommand extends Command
{
    public const NAME = 'wechat-work:check-user-avatar';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly HttpClientInterface $httpClient,
        private readonly ?FilesystemOperator $mountManager,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->getUsersWithExpiredAvatars();

        foreach ($users as $user) {
            $avatarUrl = $user->getAvatarUrl();
            if (null === $avatarUrl || '' === $avatarUrl) {
                continue;
            }

            $this->processUserAvatar($user, $output);
        }

        return Command::SUCCESS;
    }

    /**
     * @return iterable<User>
     */
    private function getUsersWithExpiredAvatars(): iterable
    {
        $qb = $this->userRepository->createQueryBuilder('u')
            ->setMaxResults(100)
        ;

        $like1 = $qb->expr()->like('u.avatarUrl', $qb->expr()->literal('https://thirdwx.qlogo.cn/%'));
        $like2 = $qb->expr()->like('u.avatarUrl', $qb->expr()->literal('https://wx.qlogo.cn/mmopen%'));

        // @phpstan-ignore-next-line return.type
        return $qb->where("u.avatarUrl != '' and u.avatarUrl is not null")
            ->andWhere($like1)
            ->orWhere($like2)
            ->getQuery()
            ->toIterable()
        ;
    }

    private function processUserAvatar(User $user, OutputInterface $output): void
    {
        $startTime = microtime(true);
        try {
            $this->logAvatarProcessStart($user, $startTime);
            $response = $this->httpClient->request('GET', $user->getAvatarUrl() ?? '');
            $this->logAvatarResponse($user, $response, $startTime);

            $newUrl = $this->handleAvatarResponse($user, $response);
            $this->updateUserAvatar($user, $newUrl, $startTime);
        } catch (\Throwable $exception) {
            $this->handleAvatarProcessingError($user, $exception, $output, $startTime);
        }
    }

    private function logAvatarProcessStart(User $user, float $startTime): void
    {
        $this->logger->info('开始请求用户头像', [
            'user_id' => $user->getId(),
            'original_url' => $user->getAvatarUrl(),
            'start_time' => $startTime,
        ]);
    }

    private function logAvatarResponse(User $user, ResponseInterface $response, float $startTime): void
    {
        $responseTime = microtime(true) - $startTime;

        $this->logger->info('头像请求完成', [
            'user_id' => $user->getId(),
            'response_time' => $responseTime,
            'status_code' => $response->getStatusCode(),
            'content_length' => strlen($response->getContent()),
        ]);
    }

    private function handleAvatarResponse(User $user, ResponseInterface $response): string
    {
        $header = $response->getHeaders();

        if (!isset($header['x-errno']) && 'notexist:-6101' !== $header['x-info'][0]) {
            return $this->saveAvatarFile($user, $response);
        }

        return $this->getDefaultAvatarUrl($user, 'original_avatar_not_exist');
    }

    private function saveAvatarFile(User $user, ResponseInterface $response): string
    {
        if (null === $this->mountManager) {
            return $this->getDefaultAvatarUrl($user, 'filesystem_unavailable');
        }

        $content = $response->getContent();
        $key = uniqid() . '.png';
        $this->mountManager->write($key, $content);
        $url = 'https://cdn.example.com/' . $key;

        $this->logger->info('头像保存成功', [
            'user_id' => $user->getId(),
            'file_key' => $key,
            'file_size' => strlen($content),
            'new_url' => $url,
        ]);

        return $url;
    }

    private function getDefaultAvatarUrl(User $user, string $reason): string
    {
        $url = $_ENV['DEFAULT_USER_AVATAR_URL'] ?? '';
        assert(is_string($url));

        $this->logger->info('使用默认头像', [
            'user_id' => $user->getId(),
            'reason' => $reason,
            'default_url' => $url,
        ]);

        return $url;
    }

    private function updateUserAvatar(User $user, string $newUrl, float $startTime): void
    {
        $oldUrl = $user->getAvatarUrl();
        $user->setAvatarUrl($newUrl);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('用户头像更新完成', [
            'user_id' => $user->getId(),
            'old_url' => $oldUrl,
            'new_url' => $newUrl,
            'total_time' => microtime(true) - $startTime,
        ]);
    }

    private function handleAvatarProcessingError(User $user, \Throwable $exception, OutputInterface $output, float $startTime): void
    {
        $this->logger->error('头像处理失败', [
            'user_id' => $user->getId(),
            'original_url' => $user->getAvatarUrl(),
            'error_message' => $exception->getMessage(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'total_time' => microtime(true) - $startTime,
        ]);

        $output->writeln($exception->getMessage());
    }
}
