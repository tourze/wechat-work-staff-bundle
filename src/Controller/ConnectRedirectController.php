<?php

namespace WechatWorkStaffBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;

class ConnectRedirectController extends AbstractController
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
    ) {
    }

    #[Route(path: '/wechat/work/connect/{corpId}/{agentId}', name: 'wechat-work-connect-redirect', methods: ['GET', 'POST'])]
    public function __invoke(
        string $corpId,
        string $agentId,
        Request $request,
    ): Response {
        $corp = $this->corpRepository->findOneBy(['corpId' => $corpId]);
        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);
        if ($agent === null) {
            throw new NotFoundHttpException('找不到指定应用');
        }

        $callbackUrl = $this->generateUrl('wechat-work-connect-callback', [
            'agentId' => $agent->getAgentId(),
            'corpId' => $corp->getCorpId(),
            'callbackUrl' => $request->query->get('callbackUrl'),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $finalUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query([
            'appid' => $corp->getCorpId(),
            'agentid' => $agent->getAgentId(),
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => 'snsapi_privateinfo',
            'state' => uniqid(),
        ]) . '#wechat_redirect';

        return $this->redirect($finalUrl);
    }
}