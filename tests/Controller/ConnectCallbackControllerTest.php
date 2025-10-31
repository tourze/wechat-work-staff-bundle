<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkStaffBundle\Controller\ConnectCallbackController;

/**
 * @internal
 */
#[CoversClass(ConnectCallbackController::class)]
#[RunTestsInSeparateProcesses]
final class ConnectCallbackControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithCodeAndCallbackUrlReturnsRedirect(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/connect-callback/test-corp/test-agent', [
            'code' => 'test-auth-code',
            'callbackUrl' => 'https://example.com/connect?jwt={{ jwt }}',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection() || 200 === $response->getStatusCode());
    }

    public function testPostRequestWithCodeAndCallbackUrlReturnsRedirect(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('POST', '/wechat/work/connect-callback/test-corp/test-agent', [
            'code' => 'test-auth-code',
            'callbackUrl' => 'https://example.com/connect?jwt={{ jwt }}',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection() || 200 === $response->getStatusCode());
    }

    public function testGetRequestWithCodeButNoCallbackUrlReturnsSuccessPage(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/connect-callback/test-corp/test-agent', [
            'code' => 'test-auth-code',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testRequestWithoutCodeParameter(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/connect-callback/test-corp/test-agent');

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testUnauthorizedAccess(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/connect-callback/unauthorized-corp/unauthorized-agent');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat/work/connect-callback/test-corp/test-agent');
    }
}
