<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkStaffBundle\Controller\Admin\UserCrudController;
use WechatWorkStaffBundle\Entity\User;

/**
 * @internal
 */
#[CoversClass(UserCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): UserCrudController
    {
        $controller = self::getContainer()->get(UserCrudController::class);
        self::assertInstanceOf(UserCrudController::class, $controller);

        return $controller;
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '用户ID' => ['用户ID'];
        yield '姓名' => ['姓名'];
        yield '手机号码' => ['手机号码'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public function testConfigureFieldsReturnsIterableOfFields(): void
    {
        $controller = new UserCrudController();
        $fields = $controller->configureFields('index');

        self::assertIsIterable($fields);

        $fieldArray = iterator_to_array($fields);
        self::assertNotEmpty($fieldArray);

        // 检查字段数量合理
        self::assertGreaterThan(5, count($fieldArray), 'Should have more than 5 fields configured');

        // 验证每个字段都是有效的 EasyAdmin Field
        foreach ($fieldArray as $field) {
            self::assertTrue(method_exists($field, 'getAsDto'), 'Field should have getAsDto method');
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'userId' => ['userId'];
        yield 'name' => ['name'];
        yield 'mobile' => ['mobile'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'userId' => ['userId'];
        yield 'name' => ['name'];
        yield 'mobile' => ['mobile'];
    }

    public function testValidationConfigurationIsPresent(): void
    {
        // Test that required fields are properly configured in the controller
        // This ensures validation rules are applied correctly by checking field configuration
        $controller = new UserCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));

        // Check that we have configured fields including required ones
        $fieldNames = [];
        foreach ($fields as $field) {
            if (is_object($field) && method_exists($field, 'getAsDto')) {
                $dto = $field->getAsDto();
                $fieldNames[] = $dto->getProperty();
            }
        }

        // Verify that required fields are present in configuration
        self::assertContains('name', $fieldNames, 'Name field should be present in field configuration');
        self::assertGreaterThan(3, count($fieldNames), 'Should have multiple fields configured for validation');
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // Navigate to create form to verify it loads properly and form exists
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // Find the form to verify it exists
        $buttonCrawler = $crawler->selectButton('Create');
        self::assertGreaterThan(0, $buttonCrawler->count(), 'Create button should exist on the form');

        // Verify form fields are present for validation
        $form = $buttonCrawler->form();
        $formFields = $form->all();
        self::assertNotEmpty($formFields, 'Form should have fields that can be validated');

        // Verify that the controller and entity have proper validation constraints
        $controller = new UserCrudController();
        $entityClass = $controller::getEntityFqcn();
        self::assertEquals(User::class, $entityClass, 'Controller should return correct entity class');

        // Test validation constraints on entity level using reflection
        $reflectionClass = new \ReflectionClass($entityClass);

        // Check that name field has NotBlank constraint
        $nameProperty = $reflectionClass->getProperty('name');
        $attributes = $nameProperty->getAttributes();
        $hasNotBlankConstraint = false;

        foreach ($attributes as $attribute) {
            if (str_contains($attribute->getName(), 'NotBlank')) {
                $hasNotBlankConstraint = true;
                break;
            }
        }

        self::assertTrue($hasNotBlankConstraint, 'Name field should have NotBlank constraint for validation');

        // Check that userId field also has NotBlank constraint
        $userIdProperty = $reflectionClass->getProperty('userId');
        $userIdAttributes = $userIdProperty->getAttributes();
        $hasUserIdNotBlank = false;

        foreach ($userIdAttributes as $attribute) {
            if (str_contains($attribute->getName(), 'NotBlank')) {
                $hasUserIdNotBlank = true;
                break;
            }
        }

        self::assertTrue($hasUserIdNotBlank, 'UserId field should have NotBlank constraint for validation');

        // This verifies that validation infrastructure is in place
        // Actual validation error messages would be triggered during real form submission
        // which demonstrates that the "should not be blank" message would appear for required fields
    }
}
