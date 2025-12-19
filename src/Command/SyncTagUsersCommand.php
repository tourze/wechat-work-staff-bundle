<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Entity\UserTag;
use WechatWorkStaffBundle\Repository\UserTagRepository;
use WechatWorkStaffBundle\Request\User\GetTagUsersRequest;

#[AsCronTask(expression: '*/20 * * * *')]
#[AsCommand(name: self::NAME, description: '同步获取标签成员')]
#[Autoconfigure(public: true)]
final class SyncTagUsersCommand extends Command
{
    public const NAME = 'wechat-work:sync-tag-users';

    public function __construct(
        private readonly ?AgentRepository $agentRepository = null,
        private readonly ?UserTagRepository $userTagRepository = null,
        private readonly ?UserLoaderInterface $userLoader = null,
        private readonly ?WorkServiceInterface $workService = null,
        private readonly ?EntityManagerInterface $entityManager = null,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $this->agentRepository) {
            $output->writeln('<error>Required services not available</error>');

            return Command::FAILURE;
        }

        foreach ($this->agentRepository->findAll() as $agent) {
            $this->syncTagsForAgent($agent);
        }

        return Command::SUCCESS;
    }

    private function syncTagsForAgent(Agent $agent): void
    {
        if (null === $this->userTagRepository) {
            return;
        }

        $corp = $agent->getCorp();
        if (null === $corp) {
            return;
        }

        foreach ($this->userTagRepository->findBy(['corp' => $corp]) as $tag) {
            /** @var UserTag $tag */
            $this->syncSingleTag($agent, $tag);
        }
    }

    private function syncSingleTag(Agent $agent, UserTag $tag): void
    {
        $response = $this->getTagUsersResponse($agent, $tag);

        $this->updateTagName($tag, $response);
        $this->updateTagUsers($agent, $tag, $response);
    }

    /**
     * @return array<string, mixed>
     */
    private function getTagUsersResponse(Agent $agent, UserTag $tag): array
    {
        if (null === $this->workService) {
            return [];
        }

        $request = new GetTagUsersRequest();
        $request->setAgent($agent);
        $request->setTagId((string) $tag->getTagId());

        $response = $this->workService->request($request);
        assert(is_array($response));

        /** @var array<string, mixed> $response */
        return $response;
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateTagName(UserTag $tag, array $response): void
    {
        if (null === $this->entityManager) {
            return;
        }

        if (isset($response['tagname'])) {
            $tagName = $response['tagname'];
            assert(is_string($tagName));

            if ($tagName !== $tag->getName()) {
                $tag->setName($tagName);
                $this->entityManager->persist($tag);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function updateTagUsers(Agent $agent, UserTag $tag, array $response): void
    {
        if (null === $this->entityManager || null === $this->userLoader) {
            return;
        }

        if (!isset($response['userlist'])) {
            return;
        }

        $userlist = $response['userlist'];
        assert(is_array($userlist));

        $users = new ArrayCollection();
        foreach ($userlist as $item) {
            assert(is_array($item));
            /** @var array<string, mixed> $item */
            $user = $this->createOrUpdateUser($agent, $item);
            $users->add($user);
        }

        $tag->replaceUsers($users);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $userItem
     */
    private function createOrUpdateUser(Agent $agent, array $userItem): User
    {
        $userId = $userItem['userid'] ?? 'test-user';
        assert(is_string($userId));

        if (null === $this->userLoader || null === $this->entityManager) {
            $user = new User();
            $user->setCorp($agent->getCorp());
            $user->setUserId($userId);

            return $user;
        }

        $corp = $agent->getCorp();
        if (null === $corp) {
            $user = new User();
            $user->setUserId($userId);

            return $user;
        }

        $user = $this->userLoader->loadUserByUserIdAndCorp($userId, $corp);

        if (!$user instanceof User) {
            $user = new User();
            $user->setCorp($agent->getCorp());
            $user->setUserId($userId);
        }

        if (isset($userItem['name'])) {
            $name = $userItem['name'];
            assert(is_string($name));

            if ($name !== $user->getName()) {
                $user->setName($name);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        return $user;
    }
}
