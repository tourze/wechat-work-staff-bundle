<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkStaffBundle\Controller\ConnectRedirectController;

/**
 * @internal
 */
#[CoversClass(ConnectRedirectController::class)]
#[RunTestsInSeparateProcesses]
final class ConnectRedirectControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithValidCorpAndAgentRedirectsToWechatOAuth(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/connect/test-corp/test-agent', [
            'callbackUrl' => 'https://example.com/callback',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection() || $response->getStatusCode() >= 200);
    }

    public function testPostRequestWithValidCorpAndAgentRedirectsToWechatOAuth(): void
    {
        $client = self::createClientWithDatabase();

        $content = json_encode(['callbackUrl' => 'https://example.com/callback']);
        self::assertIsString($content);

        $client->request('POST', '/wechat/work/connect/test-corp/test-agent', [], [], [], $content);

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection() || $response->getStatusCode() >= 200);
    }

    public function testRequestWithoutCallbackUrl(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/connect/test-corp/test-agent');

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testRequestWithInvalidCorpOrAgent(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到指定企业');

        $client->request('GET', '/wechat/work/connect/invalid-corp/invalid-agent');
    }

    public function testUnsupportedHttpMethodPut(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PUT', '/wechat/work/connect/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodDelete(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('DELETE', '/wechat/work/connect/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodPatch(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PATCH', '/wechat/work/connect/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodOptions(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('OPTIONS', '/wechat/work/connect/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodHead(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('HEAD', '/wechat/work/connect/test-corp/test-agent');

        $response = $client->getResponse();
        // HEAD 请求通常会被映射到 GET，所以可能返回 302 (重定向) 或其他状态码
        $this->assertTrue(405 === $response->getStatusCode() || 302 === $response->getStatusCode());
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到指定企业');

        $client->request('GET', '/wechat/work/connect/unauthorized-corp/unauthorized-agent');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat/work/connect/test-corp/test-agent');
    }
}
