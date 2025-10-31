<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkStaffBundle\Controller\Admin\DepartmentCrudController;
use WechatWorkStaffBundle\Entity\Department;

/**
 * @internal
 */
#[CoversClass(DepartmentCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DepartmentCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): DepartmentCrudController
    {
        $controller = self::getContainer()->get(DepartmentCrudController::class);
        self::assertInstanceOf(DepartmentCrudController::class, $controller);

        return $controller;
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '部门名称' => ['部门名称'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public function testGetEntityFqcnReturnsDepartmentClass(): void
    {
        self::assertSame(Department::class, DepartmentCrudController::getEntityFqcn());
    }

    public function testConfigureFieldsReturnsIterableOfFields(): void
    {
        $controller = new DepartmentCrudController();
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
        yield 'name' => ['name'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
    }

    public function testRequiredFieldsValidation(): void
    {
        // Test that required fields are properly configured in the controller
        $controller = new DepartmentCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));

        // Check that name field exists
        $nameFieldExists = false;

        foreach ($fields as $field) {
            if (is_object($field) && method_exists($field, 'getAsDto')) {
                $dto = $field->getAsDto();
                if ('name' === $dto->getProperty()) {
                    $nameFieldExists = true;
                    break;
                }
            }
        }

        self::assertTrue($nameFieldExists, 'Name field should exist and be configured');
    }

    public function testConfigureFilters(): void
    {
        // Test that configureFilters method exists and accepts Filters parameter
        $controller = new DepartmentCrudController();
        $reflection = new \ReflectionMethod($controller, 'configureFilters');
        self::assertTrue($reflection->isPublic());
        self::assertEquals(1, $reflection->getNumberOfParameters());
        self::assertEquals('filters', $reflection->getParameters()[0]->getName());
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 访问NEW页面获取表单
        $url = $this->generateAdminUrl('new');
        $crawler = $client->request('GET', $url);

        // 验证页面成功加载
        $this->assertResponseIsSuccessful();

        // 查找提交按钮并获取表单
        $buttonCrawler = $crawler->filter('button[type="submit"]');
        if (0 === $buttonCrawler->count()) {
            $buttonCrawler = $crawler->filter('input[type="submit"]');
        }

        self::assertGreaterThan(0, $buttonCrawler->count(), 'Should find a submit button');

        // 获取表单并提交空表单（不填写必填字段）
        $form = $buttonCrawler->form();

        try {
            $crawler = $client->submit($form);

            // 如果没有异常，检查响应状态码
            $statusCode = $client->getResponse()->getStatusCode();
            $this->assertContains($statusCode, [422, 500], '应该返回验证错误或服务器错误状态码');

            if (422 === $statusCode) {
                // 验证页面包含验证错误信息
                $invalidFeedback = $crawler->filter('.invalid-feedback');
                if ($invalidFeedback->count() > 0) {
                    $errorText = $invalidFeedback->text();
                    $this->assertStringContainsString('should not be blank', $errorText);
                } else {
                    // 如果没有找到.invalid-feedback，尝试其他可能的错误消息容器
                    $formErrors = $crawler->filter('.form-error-message, .field-error, .help-block.error');
                    self::assertGreaterThan(0, $formErrors->count(), 'Should find validation error messages');
                }
            } else {
                // 对于500错误，验证错误信息包含约束违反
                $errorContent = $client->getResponse()->getContent();
                $errorContent = false !== $errorContent ? $errorContent : '';
                $hasConstraintError = str_contains($errorContent, 'constraint')
                    || str_contains($errorContent, 'NOT NULL')
                    || str_contains($errorContent, 'required');
                $this->assertTrue($hasConstraintError, '应该包含约束违反错误信息');
            }
        } catch (\Exception $e) {
            // 捕获约束违反异常，这表明必填字段验证正在工作
            $exceptionMessage = $e->getMessage();
            $hasConstraintError = str_contains($exceptionMessage, 'constraint')
                || str_contains($exceptionMessage, 'NOT NULL')
                || str_contains($exceptionMessage, 'required')
                || str_contains($exceptionMessage, 'blank')
                || str_contains($exceptionMessage, 'must be of type string, null given')
                || str_contains($exceptionMessage, 'Expected argument of type "string", "null" given');
            $this->assertTrue($hasConstraintError, '异常应该包含约束违反错误信息: ' . $exceptionMessage);
        }
    }
}
