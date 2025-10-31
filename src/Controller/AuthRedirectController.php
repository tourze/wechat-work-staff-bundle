<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;

#[Autoconfigure(public: true)]
final class AuthRedirectController extends AbstractController
{
    public function __construct(
        private readonly ?CorpRepository $corpRepository = null,
        private readonly ?AgentRepository $agentRepository = null,
    ) {
    }

    #[Route(path: '/wechat/work/auth/{corpId}/{agentId}', name: 'wechat-work-auth-redirect', methods: ['GET', 'POST'])]
    public function __invoke(
        string $corpId,
        string $agentId,
        Request $request,
    ): Response {
        if (null === $this->corpRepository || null === $this->agentRepository) {
            throw new NotFoundHttpException('服务不可用');
        }

        $corp = $this->corpRepository->findOneBy(['corpId' => $corpId]);
        if (null === $corp) {
            throw new NotFoundHttpException('找不到指定企业');
        }

        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);
        if (null === $agent) {
            throw new NotFoundHttpException('找不到指定应用');
        }

        $callbackUrl = $this->generateUrl('wechat-work-auth-callback', [
            'agentId' => $agent->getAgentId(),
            'corpId' => $corp->getCorpId(),
            'callbackUrl' => $request->query->get('callbackUrl'),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $finalUrl = 'https://login.work.weixin.qq.com/wwlogin/sso/login?' . http_build_query([
            'login_type' => 'CorpApp',
            'appid' => $corp->getCorpId(),
            'agentid' => $agent->getAgentId(),
            'redirect_uri' => $callbackUrl,
            'state' => uniqid(),
        ]);

        return $this->redirect($finalUrl);
    }
}
