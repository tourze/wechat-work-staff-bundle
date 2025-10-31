<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WeuiBundle\Service\NoticeService;

#[Autoconfigure(public: true)]
final class AuthCallbackController extends AbstractController
{
    public function __construct(
        private readonly GetWechatWorkUserByAuthCode $codeApi,
        private readonly ?NoticeService $noticeService,
        private readonly Environment $twig,
    ) {
    }

    #[Route(path: '/wechat/work/auth-callback/{corpId}/{agentId}', name: 'wechat-work-auth-callback', methods: ['GET', 'POST'])]
    public function __invoke(
        string $corpId,
        string $agentId,
        Request $request,
    ): Response {
        $result = $this->codeApi->getResult(
            $corpId,
            $agentId,
            (string) $request->query->get('code'),
        );

        $callbackUrl = $request->query->get('callbackUrl');
        if (null !== $callbackUrl && '' !== $callbackUrl) {
            // https://127.0.0.1/index?jwt={{ jwt }}
            $twigTemplate = $this->twig->createTemplate((string) $callbackUrl);
            $callbackUrl = $twigTemplate->render([
                ...$result,
                'jwt' => $result['jwt'],
            ]);

            return $this->redirect($callbackUrl);
        }

        $userName = $result['name'] ?? '';
        assert(is_string($userName));

        if (null === $this->noticeService) {
            return new Response("授权成功，欢迎你，{$userName}");
        }

        return $this->noticeService->weuiSuccess('授权成功', "欢迎你，{$userName}");
    }
}
