<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Request\User\ListIdRequest;

#[Autoconfigure(public: true)]
final class TestSimpleUserListController extends AbstractController
{
    public function __construct(
        private readonly ?CorpRepository $corpRepository = null,
        private readonly ?AgentRepository $agentRepository = null,
        private readonly ?WorkServiceInterface $workService = null,
    ) {
    }

    #[Route(path: '/wechat/work/test/simple_user_list', name: 'wechat_work_test_simple_user_list', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        if (null === $this->workService) {
            return $this->json(['error' => '服务不可用'], 503);
        }

        $agent = $this->getAgent($request);
        if (null === $agent) {
            return $this->json(['error' => 'Agent not found'], 404);
        }

        $cursor = $request->query->get('cursor');
        $limit = (int) ($request->query->get('limit') ?? 1000);

        $apiRequest = new ListIdRequest();
        $apiRequest->setAgent($agent);
        if (null !== $cursor && '' !== $cursor) {
            $apiRequest->setCursor((string) $cursor);
        }
        $apiRequest->setLimit($limit);
        $response = $this->workService->request($apiRequest);

        return $this->json($response);
    }

    protected function getAgent(Request $request): ?AgentInterface
    {
        if (null === $this->corpRepository || null === $this->agentRepository) {
            return null;
        }

        $corpId = $request->query->get('corpId');
        if (null === $corpId) {
            return null;
        }

        $corp = null;
        if (is_numeric($corpId)) {
            $corp = $this->corpRepository->find((int) $corpId);
        }

        if (null === $corp) {
            $corp = $this->corpRepository->findOneBy([
                'corpId' => $corpId,
            ]);
        }

        if (null === $corp) {
            return null;
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
        ], ['id' => 'ASC']);
    }
}
