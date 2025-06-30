<?php

namespace WechatWorkStaffBundle\Tests\Integration\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkStaffBundle\Controller\AuthRedirectController;

class AuthRedirectControllerTest extends TestCase
{
    /**
     * @var CorpRepository&MockObject
     */
    private CorpRepository $corpRepository;
    
    /**
     * @var AgentRepository&MockObject
     */
    private AgentRepository $agentRepository;
    
    private AuthRedirectController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        
        $this->controller = new AuthRedirectController(
            $this->corpRepository,
            $this->agentRepository
        );
    }

    public function testInvoke_withValidCorpAndAgent_redirectsToWechatLogin(): void
    {
        $request = new Request();
        $request->query->set('callbackUrl', 'https://example.com/callback');
        
        $corp = $this->createMock(Corp::class);
        $corp->expects($this->atLeastOnce())
            ->method('getCorpId')
            ->willReturn('corp123');
            
        $agent = $this->createMock(Agent::class);
        $agent->expects($this->atLeastOnce())
            ->method('getAgentId')
            ->willReturn('agent456');
        
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent456',
            ])
            ->willReturn($agent);
        
        $controllerClass = new class($this->corpRepository, $this->agentRepository) extends AuthRedirectController {
            public string $generatedUrl = '';
            public string $redirectUrl = '';
            
            protected function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
            {
                $this->generatedUrl = 'https://myapp.com/wechat/work/auth-callback/corp123/agent456?callbackUrl=https://example.com/callback';
                return $this->generatedUrl;
            }
            
            protected function redirect(string $url, int $status = 302): RedirectResponse
            {
                $this->redirectUrl = $url;
                return new RedirectResponse($url, $status);
            }
        };
        
        $response = $controllerClass->__invoke('corp123', 'agent456', $request);
        
        $this->assertStringStartsWith('https://login.work.weixin.qq.com/wwlogin/sso/login?', $controllerClass->redirectUrl);
        $this->assertStringContainsString('login_type=CorpApp', $controllerClass->redirectUrl);
        $this->assertStringContainsString('appid=corp123', $controllerClass->redirectUrl);
        $this->assertStringContainsString('agentid=agent456', $controllerClass->redirectUrl);
        
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testInvoke_withInvalidAgent_throwsNotFoundException(): void
    {
        $request = new Request();
        
        $corp = $this->createMock(Corp::class);
        
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent456',
            ])
            ->willReturn(null);
        
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到指定应用');
        
        $this->controller->__invoke('corp123', 'agent456', $request);
    }
}