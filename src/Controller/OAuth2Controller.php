<?php

namespace WechatWorkStaffBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WeuiBundle\Service\NoticeService;

/**
 * @see https://developer.work.weixin.qq.com/document/path/96440
 */
#[Route(path: '/wechat/work')]
class OAuth2Controller extends AbstractController
{
    public function __construct(
        private readonly GetWechatWorkUserByAuthCode $codeApi,
        private readonly NoticeService $noticeService,
    ) {
    }

    #[Route(path: '/auth/{corpId}/{agentId}', name: 'wechat-work-auth-redirect', methods: ['GET', 'POST'])]
    public function authRedirect(
        string $corpId,
        string $agentId,
        Request $request,
        CorpRepository $corpRepository,
        AgentRepository $agentRepository,
    ): Response {
        $corp = $corpRepository->findOneBy(['corpId' => $corpId]);
        $agent = $agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);
        if (!$agent) {
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

    #[Route(path: '/auth-callback/{corpId}/{agentId}', name: 'wechat-work-auth-callback', methods: ['GET', 'POST'])]
    public function authCallback(
        string $corpId,
        string $agentId,
        Request $request,
        Environment $twig,
    ): Response {
        $result = $this->codeApi->getResult(
            $corpId,
            $agentId,
            $request->query->get('code'),
        );

        $callbackUrl = $request->query->get('callbackUrl');
        if ($callbackUrl) {
            // https://127.0.0.1/index?jwt={{ jwt }}
            $twigTemplate = $twig->createTemplate($callbackUrl);
            $callbackUrl = $twigTemplate->render([
                ...$result,
                'jwt' => $result['jwt'],
            ]);

            return $this->redirect($callbackUrl);
        }

        return $this->noticeService->weuiSuccess('授权成功', "欢迎你，{$result['name']}");
    }

    #[Route(path: '/connect/{corpId}/{agentId}', name: 'wechat-work-connect-redirect', methods: ['GET', 'POST'])]
    public function connectRedirect(
        string $corpId,
        string $agentId,
        Request $request,
        CorpRepository $corpRepository,
        AgentRepository $agentRepository,
    ): Response {
        $corp = $corpRepository->findOneBy(['corpId' => $corpId]);
        $agent = $agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);
        if (!$agent) {
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

    #[Route(path: '/auth-callback/{corpId}/{agentId}', name: 'wechat-work-connect-callback', methods: ['GET', 'POST'])]
    public function connectCallback(
        string $corpId,
        string $agentId,
        Request $request,
        Environment $twig,
    ): Response {
        $result = $this->codeApi->getResult(
            $corpId,
            $agentId,
            $request->query->get('code'),
        );

        $callbackUrl = $request->query->get('callbackUrl');
        if ($callbackUrl) {
            // https://127.0.0.1/index?jwt={{ jwt }}
            $twigTemplate = $twig->createTemplate($callbackUrl);
            $callbackUrl = $twigTemplate->render([
                ...$result,
                'jwt' => $result['jwt'],
            ]);

            return $this->redirect($callbackUrl);
        }

        return $this->noticeService->weuiSuccess('授权成功', "欢迎你，{$result['name']}");
    }
}
