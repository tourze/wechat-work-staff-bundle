<?php

namespace WechatWorkStaffBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WeuiBundle\Service\NoticeService;

class ConnectCallbackController extends AbstractController
{
    public function __construct(
        private readonly GetWechatWorkUserByAuthCode $codeApi,
        private readonly NoticeService $noticeService,
        private readonly Environment $twig,
    ) {
    }

    #[Route(path: '/wechat/work/connect-callback/{corpId}/{agentId}', name: 'wechat-work-connect-callback', methods: ['GET', 'POST'])]
    public function __invoke(
        string $corpId,
        string $agentId,
        Request $request,
    ): Response {
        $result = $this->codeApi->getResult(
            $corpId,
            $agentId,
            $request->query->get('code'),
        );

        $callbackUrl = $request->query->get('callbackUrl');
        if ($callbackUrl) {
            // https://127.0.0.1/index?jwt={{ jwt }}
            $twigTemplate = $this->twig->createTemplate($callbackUrl);
            $callbackUrl = $twigTemplate->render([
                ...$result,
                'jwt' => $result['jwt'],
            ]);

            return $this->redirect($callbackUrl);
        }

        return $this->noticeService->weuiSuccess('授权成功', "欢迎你，{$result['name']}");
    }
}