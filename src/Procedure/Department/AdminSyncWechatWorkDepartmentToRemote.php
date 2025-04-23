<?php

namespace WechatWorkStaffBundle\Procedure\Department;

use AntdCpBundle\Builder\Action\ApiCallAction;
use AppBundle\Procedure\Base\ApiCallActionProcedure;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCSecurityBundle\Attribute\MethodPermission;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Repository\DepartmentRepository;
use WechatWorkStaffBundle\Request\Department\DepartmentCreateRequest;
use WechatWorkStaffBundle\Request\Department\DepartmentUpdateRequest;

#[Log]
#[MethodExpose(AdminSyncWechatWorkDepartmentToRemote::NAME)]
#[IsGranted('ROLE_OPERATOR')]
#[MethodPermission(permission: Department::class . '::renderSyncToRemoteAction', title: '上传')]
class AdminSyncWechatWorkDepartmentToRemote extends ApiCallActionProcedure
{
    public const NAME = 'AdminSyncWechatWorkDepartmentToRemote';

    public function __construct(
        private readonly DepartmentRepository $departmentRepository,
        private readonly WorkService $workService,
    ) {
    }

    public function getAction(): ApiCallAction
    {
        return ApiCallAction::gen()
            ->setLabel('上传')
            ->setConfirmText('是否确认要上传本地信息到远程')
            ->setApiName(AdminSyncWechatWorkDepartmentToRemote::NAME);
    }

    public function execute(): array
    {
        $that = $this->departmentRepository->findOneBy(['id' => $this->id]);
        if (!$that) {
            throw new ApiException('找不到记录');
        }

        if ($that->getRemoteId()) {
            $request = new DepartmentUpdateRequest();
            $request->setId($that->getRemoteId());
        } else {
            $request = new DepartmentCreateRequest();
        }

        $request->setAgent($that->getAgent());
        $request->setName($that->getName());
        $request->setOrder($that->getSortNumber() ?: 0);
        $request->setParentId($that->getParent() ? $that->getParent()->getRemoteId() : 0);

        try {
            $res = $this->workService->request($request);
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }

        if (isset($res['id'])) {
            $that->setRemoteId($res['id']);
        }

        $this->departmentRepository->save($this);

        return [
            '__message' => '同步成功',
        ];
    }
}
