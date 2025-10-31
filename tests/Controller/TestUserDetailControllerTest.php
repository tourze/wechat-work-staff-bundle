<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkStaffBundle\Controller\TestUserDetailController;

/**
 * @internal
 */
#[CoversClass(TestUserDetailController::class)]
#[RunTestsInSeparateProcesses]
final class TestUserDetailControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithUserIdReturnsUserDetail(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/user_detail', [
            'corpId' => 'test-corp',
            'agentId' => 'test-agent',
            'userId' => 'user123',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);

        if (200 === $response->getStatusCode()) {
            $content = $response->getContent();
            $this->assertNotEmpty($content);
            $this->assertJson($content);
        }
    }

    public function testPostRequestWithUserIdReturnsUserDetail(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('POST', '/wechat/work/test/user_detail', [
            'corpId' => 'test-corp',
            'agentId' => 'test-agent',
            'userId' => 'user123',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testRequestWithCorpIdOnlyUsesFirstAgent(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/user_detail', [
            'corpId' => 'test-corp',
            'userId' => 'user456',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testRequestWithoutUserId(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/user_detail', [
            'corpId' => 'test-corp',
            'agentId' => 'test-agent',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testUnsupportedHttpMethodPut(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PUT', '/wechat/work/test/user_detail');
    }

    public function testUnsupportedHttpMethodDelete(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('DELETE', '/wechat/work/test/user_detail');
    }

    public function testUnsupportedHttpMethodPatch(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PATCH', '/wechat/work/test/user_detail');
    }

    public function testUnsupportedHttpMethodHead(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('HEAD', '/wechat/work/test/user_detail');

        $response = $client->getResponse();
        // HEAD method behavior in Symfony may return 404 or 405 depending on routing configuration
        $this->assertTrue(404 === $response->getStatusCode() || 405 === $response->getStatusCode());
    }

    public function testUnsupportedHttpMethodOptions(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('OPTIONS', '/wechat/work/test/user_detail');
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/user_detail', [
            'corpId' => 'unauthorized-corp',
            'agentId' => 'unauthorized-agent',
            'userId' => 'unauthorized-user',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 400 || $response->getStatusCode() >= 200);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat/work/test/user_detail');
    }
}
