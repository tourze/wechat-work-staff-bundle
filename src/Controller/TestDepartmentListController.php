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

class TestDepartmentListController extends AbstractController
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
    ) {}

    #[Route('/wechat/work/test/department_list', name: 'wechat_work_test_department_list')]
    public function __invoke(Request $request): Response
    {
        $agent = $this->getAgent($request);

        $apiRequest = new GetDepartmentListRequest();
        $apiRequest->setAgent($agent);
        $response = $this->workService->request($apiRequest);

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