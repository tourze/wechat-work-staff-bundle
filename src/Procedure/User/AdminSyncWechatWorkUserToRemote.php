<?php

namespace WechatWorkStaffBundle\Procedure\User;

use AntdCpBundle\Builder\Action\ApiCallAction;
use AppBundle\Procedure\Base\ApiCallActionProcedure;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCSecurityBundle\Attribute\MethodPermission;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Request\User\UpdateUserRequest;

#[Log]
#[MethodExpose(AdminSyncWechatWorkUserToRemote::NAME)]
#[IsGranted('ROLE_OPERATOR')]
#[MethodPermission(permission: User::class . '::renderSyncToRemoteAction', title: '上传')]
class AdminSyncWechatWorkUserToRemote extends ApiCallActionProcedure
{
    public const NAME = 'AdminSyncWechatWorkUserToRemote';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly WorkService $workService,
    ) {
    }

    public function getAction(): ApiCallAction
    {
        return ApiCallAction::gen()
            ->setLabel('上传')
            ->setConfirmText('是否确认要上传本地信息到远程')
            ->setApiName(AdminSyncWechatWorkUserToRemote::NAME);
    }

    public function execute(): array
    {
        $that = $this->userRepository->findOneBy(['id' => $this->id]);
        if (!$that) {
            throw new ApiException('找不到记录');
        }

        $request = new UpdateUserRequest();
        $request->setAgent($that->getAgent());
        $request->setUserId($that->getUserId());
        $request->setName($that->getName());
        $request->setAlias($that->getAlias());
        $request->setMobile($that->getMobile());
        $request->setEmail($that->getEmail());
        $this->workService->request($request);

        return [
            '__message' => '同步成功',
        ];
    }
}
