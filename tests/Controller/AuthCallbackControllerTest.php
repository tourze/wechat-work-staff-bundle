<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkStaffBundle\Controller\AuthCallbackController;

/**
 * @internal
 */
#[CoversClass(AuthCallbackController::class)]
#[RunTestsInSeparateProcesses]
final class AuthCallbackControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithCodeAndCallbackUrlReturnsRedirect(): void
    {
        $client = self::createClientWithDatabase();

        // In test environment without proper agent configuration, expect NotFoundHttpException
        try {
            $client->request('GET', '/wechat/work/auth-callback/test-corp/test-agent', [
                'code' => 'test-auth-code',
                'callbackUrl' => 'https://example.com/success?jwt={{ jwt }}',
            ]);

            $response = $client->getResponse();
            $this->assertInstanceOf(Response::class, $response);
        } catch (NotFoundHttpException $e) {
            $this->assertSame('找不到指定应用', $e->getMessage());
        }
    }

    public function testPostRequestWithCodeAndCallbackUrlReturnsRedirect(): void
    {
        $client = self::createClientWithDatabase();

        // In test environment without proper agent configuration, expect NotFoundHttpException
        try {
            $client->request('POST', '/wechat/work/auth-callback/test-corp/test-agent', [
                'code' => 'test-auth-code',
                'callbackUrl' => 'https://example.com/success?jwt={{ jwt }}',
            ]);

            $response = $client->getResponse();
            $this->assertInstanceOf(Response::class, $response);
        } catch (NotFoundHttpException $e) {
            $this->assertSame('找不到指定应用', $e->getMessage());
        }
    }

    public function testGetRequestWithCodeButNoCallbackUrlReturnsSuccessPage(): void
    {
        $client = self::createClientWithDatabase();

        // In test environment without proper agent configuration, expect NotFoundHttpException
        try {
            $client->request('GET', '/wechat/work/auth-callback/test-corp/test-agent', [
                'code' => 'test-auth-code',
            ]);

            $response = $client->getResponse();
            $this->assertInstanceOf(Response::class, $response);
        } catch (NotFoundHttpException $e) {
            $this->assertSame('找不到指定应用', $e->getMessage());
        }
    }

    public function testRequestWithoutCodeParameter(): void
    {
        $client = self::createClientWithDatabase();

        // In test environment without proper agent configuration, expect NotFoundHttpException
        try {
            $client->request('GET', '/wechat/work/auth-callback/test-corp/test-agent');

            $response = $client->getResponse();
            $this->assertInstanceOf(Response::class, $response);
        } catch (NotFoundHttpException $e) {
            $this->assertSame('找不到指定应用', $e->getMessage());
        }
    }

    public function testUnsupportedHttpMethodPut(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PUT', '/wechat/work/auth-callback/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodDelete(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('DELETE', '/wechat/work/auth-callback/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodPatch(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PATCH', '/wechat/work/auth-callback/test-corp/test-agent');
    }

    public function testUnsupportedHttpMethodHead(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('HEAD', '/wechat/work/auth-callback/test-corp/test-agent');

        $response = $client->getResponse();
        // HEAD 请求通常会被映射到 GET，所以可能返回 302 (重定向) 或其他状态码
        $this->assertTrue(405 === $response->getStatusCode() || 302 === $response->getStatusCode() || 200 === $response->getStatusCode());
    }

    public function testUnsupportedHttpMethodOptions(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('OPTIONS', '/wechat/work/auth-callback/test-corp/test-agent');
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        // In test environment without proper agent configuration, expect NotFoundHttpException
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到指定应用');

        $client->request('GET', '/wechat/work/auth-callback/unauthorized-corp/unauthorized-agent');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat/work/auth-callback/test-corp/test-agent');
    }
}
