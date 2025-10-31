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
use WechatWorkStaffBundle\Request\User\GetUserRequest;

#[Autoconfigure(public: true)]
final class TestUserDetailController extends AbstractController
{
    public function __construct(
        private readonly ?CorpRepository $corpRepository = null,
        private readonly ?AgentRepository $agentRepository = null,
        private readonly ?WorkServiceInterface $workService = null,
    ) {
    }

    #[Route(path: '/wechat/work/test/user_detail', name: 'wechat_work_test_user_detail', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        if (null === $this->workService) {
            return $this->json(['error' => '服务不可用'], 503);
        }

        $agent = $this->getAgent($request);
        if (null === $agent) {
            return $this->json(['error' => 'Agent not found'], 404);
        }

        $userId = $request->query->get('userId');

        $apiRequest = new GetUserRequest();
        $apiRequest->setAgent($agent);
        $apiRequest->setUserId((string) ($userId ?? ''));
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
