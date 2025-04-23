<?php

namespace WechatWorkStaffBundle\MessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Message\SyncUserListMessage;
use WechatWorkStaffBundle\Repository\DepartmentRepository;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Request\User\ListIdRequest;
use WechatWorkStaffBundle\Service\BizUserService;

#[AsMessageHandler]
class SyncUserListHandler
{
    public function __construct(
        private readonly PropertyAccessor $propertyAccessor,
        private readonly AgentRepository $agentRepository,
        private readonly UserRepository $userRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly BizUserService $bizUserService,
        private readonly WorkService $workService,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SyncUserListMessage $message): void
    {
        $agent = $this->agentRepository->find([
            'id' => $message->getAgentId(),
        ]);

        $nextCursor = null;
        do {
            $request = new ListIdRequest();
            $request->setCursor($nextCursor);
            $request->setLimit(200); // 减少一些默认数据？200一次应该差不多
            $request->setAgent($agent);

            $list = $this->workService->request($request);

            // 记录用于下次循环
            if (isset($list['next_cursor']) && $list['next_cursor']) {
                $nextCursor = $list['next_cursor'];
            }
            if (!isset($list['dept_user'])) {
                $this->logger->error('拉取用户列表时发生错误', [
                    'request' => $request,
                    'list' => $list,
                ]);
                break;
            }

            foreach ($list['dept_user'] as $item) {
                $user = $this->userRepository->findOneBy([
                    'userId' => $item['open_userid'],
                    'corp' => $agent->getCorp(),
                ]);
                if (!$user) {
                    $user = new User();
                    $user->setUserId($item['open_userid']);
                    $user->setCorp($agent->getCorp());
                }

                $user->setAgent($agent);
                $user->setName($item['open_userid']); // TODO 当前拿不到用户名称那些信息喔？我们暂时塞 userid 进去

                // 处理部门信息
                $currentDepartments = [];
                if (isset($item['department'])) {
                    if (!is_array($item['department'])) {
                        $item['department'] = [
                            $item['department'],
                        ];
                    }

                    foreach ($item['department'] as $departmentId) {
                        $tmp = $this->departmentRepository->findOneBy([
                            'remoteId' => $departmentId,
                            'corp' => $user->getCorp(),
                        ]);
                        if ($tmp) {
                            $currentDepartments[] = $tmp;
                        }
                    }
                }

                $this->propertyAccessor->setValue($user, 'departments', $currentDepartments);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->bizUserService->transformFromWorkUser($user);
            }
        } while (null !== $nextCursor);
    }
}
