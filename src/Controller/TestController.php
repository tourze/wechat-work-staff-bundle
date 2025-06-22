<?php

namespace WechatWorkStaffBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;
use WechatWorkStaffBundle\Request\User\GetUserRequest;
use WechatWorkStaffBundle\Request\User\GetUserSimpleListRequest;
use WechatWorkStaffBundle\Request\User\ListIdRequest;

#[Route(path: '/wechat/work/test')]
class TestController extends AbstractController
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
    ) {}

    #[Route('/department_list')]
    public function departmentList(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $request = new GetDepartmentListRequest();
        $request->setAgent($agent);
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    #[Route('/simple_user_list')]
    public function simpleUserList(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $departmentId = $request->query->get('departmentId');

        $request = new GetUserSimpleListRequest();
        $request->setAgent($agent);
        $request->setDepartmentId($departmentId);
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    #[Route('/get_user_id_list')]
    public function userIdList(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $request = new ListIdRequest();
        $request->setAgent($agent);
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    #[Route('/get_user_detail')]
    public function userDetail(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $userId = $request->query->get('userId');

        $request = new GetUserRequest();
        $request->setAgent($agent);
        $request->setUserId($userId);
        $response = $this->workService->request($request);

        return $this->json($response);
    }

    protected function getAgent(Request $request): AgentInterface
    {
        $corp = $this->corpRepository->find($request->query->get('corpId'));
        if (null === $corp) {
            $corp = $this->corpRepository->findOneBy([
                'corpId' => $request->query->get('corpId'),
            ]);
        }

        if ($request->query->has('agentId')) {
            return $this->agentRepository->findOneBy([
                'corp' => $corp,
                'agentId' => $request->query->get('agentId'),
            ]);
        }

        // 默认拿第一个
        return $this->agentRepository->findOneBy([
            'corp' => $corp,
        ], ['id' => Criteria::ASC]);
    }
}
