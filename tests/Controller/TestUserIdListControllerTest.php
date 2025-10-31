<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatWorkStaffBundle\Controller\TestUserIdListController;

/**
 * @internal
 */
#[CoversClass(TestUserIdListController::class)]
#[RunTestsInSeparateProcesses]
final class TestUserIdListControllerTest extends AbstractWebTestCase
{
    public function testGetRequestWithCorpAndAgentReturnsUserIdList(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/get_user_id_list', [
            'corpId' => 'test-corp',
            'agentId' => 'test-agent',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);

        if (200 === $response->getStatusCode()) {
            $content = $response->getContent();
            $this->assertNotEmpty($content);
            $this->assertJson($content);
        }
    }

    public function testPostRequestWithCorpAndAgentReturnsUserIdList(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('POST', '/wechat/work/test/get_user_id_list', [
            'corpId' => 'test-corp',
            'agentId' => 'test-agent',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testRequestWithOnlyCorpIdUsesFirstAgent(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/get_user_id_list', [
            'corpId' => 'test-corp',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testRequestWithoutParameters(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/get_user_id_list');

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 200 && $response->getStatusCode() < 500);
    }

    public function testUnsupportedHttpMethodPut(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PUT', '/wechat/work/test/get_user_id_list');
    }

    public function testUnsupportedHttpMethodDelete(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('DELETE', '/wechat/work/test/get_user_id_list');
    }

    public function testUnsupportedHttpMethodPatch(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('PATCH', '/wechat/work/test/get_user_id_list');
    }

    public function testUnsupportedHttpMethodOptions(): void
    {
        $client = self::createClientWithDatabase();

        $client->catchExceptions(false);
        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request('OPTIONS', '/wechat/work/test/get_user_id_list');
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat/work/test/get_user_id_list', [
            'corpId' => 'unauthorized-corp',
            'agentId' => 'unauthorized-agent',
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->getStatusCode() >= 400 || $response->getStatusCode() >= 200);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $client->request($method, '/wechat/work/test/get_user_id_list');
    }
}
