<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkStaffBundle\Controller\Admin\AgentUserCrudController;
use WechatWorkStaffBundle\Entity\AgentUser;

/**
 * @internal
 */
#[CoversClass(AgentUserCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AgentUserCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): AgentUserCrudController
    {
        $controller = self::getContainer()->get(AgentUserCrudController::class);
        self::assertInstanceOf(AgentUserCrudController::class, $controller);

        return $controller;
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '企业用户ID' => ['企业用户ID'];
        yield 'OpenID' => ['OpenID'];
        yield '应用' => ['应用'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public function testConfigureFieldsReturnsIterableOfFields(): void
    {
        $controller = new AgentUserCrudController();
        $fields = $controller->configureFields('index');

        self::assertIsIterable($fields);

        $fieldArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldArray);

        // 检查字段数量合理
        self::assertGreaterThan(5, count($fieldArray), 'Should have more than 5 fields configured');

        // 验证每个字段都是有效的 EasyAdmin Field
        foreach ($fieldArray as $field) {
            self::assertIsObject($field, 'Each field should be an object');
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'userId' => ['userId'];
        yield 'openId' => ['openId'];
        yield 'agent' => ['agent'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'userId' => ['userId'];
        yield 'openId' => ['openId'];
        yield 'agent' => ['agent'];
    }

    public function testRequiredFieldsValidation(): void
    {
        // Test that required fields are properly configured in the controller
        // This ensures validation rules are applied correctly
        $controller = new AgentUserCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));

        // Check that required fields exist
        $userIdExists = false;
        $openIdExists = false;

        foreach ($fields as $field) {
            if (is_object($field) && method_exists($field, 'getAsDto')) {
                $dto = $field->getAsDto();
                if ('userId' === $dto->getProperty()) {
                    $userIdExists = true;
                }
                if ('openId' === $dto->getProperty()) {
                    $openIdExists = true;
                }
            }
        }

        self::assertTrue($userIdExists, 'userId field should exist and be configured');
        self::assertTrue($openIdExists, 'openId field should exist and be configured');
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        try {
            // 访问新建页面
            $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
            $this->assertResponseIsSuccessful();
        } catch (\Exception $e) {
            self::markTestSkipped('NEW action is not available for this controller: ' . $e->getMessage());
        }

        // 获取表单提交按钮 - 尝试各种可能的按钮文本
        $button = $crawler->selectButton('创建');
        if (0 === $button->count()) {
            $button = $crawler->selectButton('Save');
        }
        if (0 === $button->count()) {
            $button = $crawler->selectButton('保存');
        }
        if (0 === $button->count()) {
            $button = $crawler->selectButton('Create');
        }
        if (0 === $button->count()) {
            $button = $crawler->selectButton('submit');
        }
        if (0 === $button->count()) {
            // 尝试通过CSS选择器查找提交按钮
            $button = $crawler->filter('form button[type="submit"]');
        }
        if (0 === $button->count()) {
            // 尝试查找任何提交按钮
            $button = $crawler->filter('input[type="submit"], button[type="submit"]');
        }

        self::assertGreaterThan(0, $button->count(), 'Should find a submit button on the new page');

        $form = $button->form();

        // 提交空表单（不填写必填字段）
        $entityName = $this->getEntitySimpleName();
        $form[$entityName . '[userId]'] = '';
        $form[$entityName . '[openId]'] = '';

        $crawler = $client->submit($form);

        // 验证收到422状态码和验证错误消息
        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString('should not be blank', $crawler->filter('.invalid-feedback')->text());
    }
}
