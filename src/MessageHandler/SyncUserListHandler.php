<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Message\SyncUserListMessage;
use WechatWorkStaffBundle\Repository\DepartmentRepository;
use WechatWorkStaffBundle\Request\User\ListIdRequest;
use WechatWorkStaffBundle\Service\BizUserService;

#[Autoconfigure(public: true)]
#[AsMessageHandler]
class SyncUserListHandler
{
    public function __construct(
        private readonly ?AgentRepository $agentRepository = null,
        private readonly ?UserLoaderInterface $userLoader = null,
        private readonly ?DepartmentRepository $departmentRepository = null,
        private readonly ?BizUserService $bizUserService = null,
        private readonly ?WorkServiceInterface $workService = null,
        private readonly ?LoggerInterface $logger = null,
        private readonly ?EntityManagerInterface $entityManager = null,
    ) {
    }

    public function __invoke(SyncUserListMessage $message): void
    {
        if (null === $this->agentRepository || null === $this->workService || null === $this->entityManager) {
            return;
        }

        $agent = $this->agentRepository->find($message->getAgentId());
        assert($agent instanceof Agent || null === $agent);

        if (null === $agent) {
            if (null !== $this->logger) {
                $this->logger->warning('Agent not found', ['agentId' => $message->getAgentId()]);
            }

            return;
        }

        $nextCursor = null;
        do {
            $list = $this->fetchUserList($agent, $nextCursor);
            $nextCursor = $this->processUserList($agent, $list);
        } while (null !== $nextCursor);
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchUserList(Agent $agent, ?string $cursor): array
    {
        $request = new ListIdRequest();
        $request->setCursor($cursor);
        $request->setLimit(200);
        $request->setAgent($agent);

        if (null === $this->workService) {
            return [];
        }

        $response = $this->workService->request($request);
        assert(is_array($response));

        /** @var array<string, mixed> $response */
        return $response;
    }

    /**
     * @param array<string, mixed> $list
     */
    private function processUserList(Agent $agent, array $list): ?string
    {
        $nextCursor = $this->extractNextCursor($list);

        if (!$this->isValidUserList($list)) {
            return null;
        }

        $deptUser = $list['dept_user'];
        assert(is_array($deptUser));

        foreach ($deptUser as $item) {
            if (!is_array($item)) {
                continue;
            }
            /** @var array<string, mixed> $item */
            $this->processUserItem($agent, $item);
        }

        return $nextCursor;
    }

    /**
     * @param array<string, mixed> $list
     */
    private function extractNextCursor(array $list): ?string
    {
        if (!isset($list['next_cursor'])) {
            return null;
        }

        $nextCursor = $list['next_cursor'];
        if (!is_string($nextCursor) || '' === $nextCursor) {
            return null;
        }

        return $nextCursor;
    }

    /**
     * @param array<string, mixed> $list
     */
    private function isValidUserList(array $list): bool
    {
        if (!isset($list['dept_user'])) {
            if (null !== $this->logger) {
                $this->logger->error('拉取用户列表时发生错误', ['list' => $list]);
            }

            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function processUserItem(Agent $agent, array $item): void
    {
        $user = $this->createOrLoadUser($agent, $item);

        $this->updateUserData($agent, $user, $item);
        $this->updateUserDepartments($user, $item);
        $this->saveUser($user);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function createOrLoadUser(Agent $agent, array $item): User
    {
        $openUserId = $item['open_userid'];
        assert(is_string($openUserId));

        if (null === $this->userLoader) {
            $user = new User();
            $user->setUserId($openUserId);
            $user->setCorp($agent->getCorp());

            return $user;
        }

        $corp = $agent->getCorp();
        if (null === $corp) {
            $user = new User();
            $user->setUserId($openUserId);

            return $user;
        }

        $user = $this->userLoader->loadUserByUserIdAndCorp($openUserId, $corp);

        if (null === $user || !$user instanceof User) {
            $user = new User();
            $user->setUserId($openUserId);
            $user->setCorp($agent->getCorp());
        }

        return $user;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function updateUserData(Agent $agent, User $user, array $item): void
    {
        $openUserId = $item['open_userid'];
        assert(is_string($openUserId));

        $user->setAgent($agent);
        $user->setName($openUserId);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function updateUserDepartments(User $user, array $item): void
    {
        $newDepartments = $this->extractUserDepartments($user, $item);

        // 清除现有部门关系
        foreach ($user->getDepartments()->toArray() as $department) {
            $user->removeDepartment($department);
        }

        // 添加新的部门关系
        foreach ($newDepartments as $department) {
            /** @var Department $department */
            $user->addDepartment($department);
        }
    }

    /**
     * @param array<string, mixed> $item
     * @return array<int, object>
     */
    private function extractUserDepartments(User $user, array $item): array
    {
        if (!isset($item['department'])) {
            return [];
        }

        $departmentIds = is_array($item['department']) ? $item['department'] : [$item['department']];
        $departments = [];

        foreach ($departmentIds as $departmentId) {
            if (null === $this->departmentRepository) {
                continue;
            }

            $department = $this->departmentRepository->findOneBy([
                'remoteId' => $departmentId,
                'corp' => $user->getCorp(),
            ]);

            if (null !== $department) {
                $departments[] = $department;
            }
        }

        return $departments;
    }

    private function saveUser(User $user): void
    {
        if (null === $this->entityManager) {
            return;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if (null !== $this->bizUserService) {
            $this->bizUserService->transformFromWorkUser($user);
        }
    }
}
