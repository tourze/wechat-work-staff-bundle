<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkStaffBundle\Controller\Admin\UserTagCrudController;
use WechatWorkStaffBundle\Entity\UserTag;

/**
 * @internal
 */
#[CoversClass(UserTagCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserTagCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): UserTagCrudController
    {
        $controller = self::getContainer()->get(UserTagCrudController::class);
        self::assertInstanceOf(UserTagCrudController::class, $controller);

        return $controller;
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '标签名称' => ['标签名称'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public function testGetEntityFqcnReturnsUserTagClass(): void
    {
        self::assertSame(UserTag::class, UserTagCrudController::getEntityFqcn());
    }

    public function testConfigureFieldsReturnsIterableOfFields(): void
    {
        $controller = new UserTagCrudController();
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
        yield 'name' => ['name'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
    }

    public function testValidationConfigurationIsPresent(): void
    {
        // Test that required fields are properly configured in the controller
        // This ensures validation rules are applied correctly by checking field configuration
        $controller = new UserTagCrudController();
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
        self::assertGreaterThan(2, count($fieldNames), 'Should have multiple fields configured for validation');
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // Navigate to the new page to get the form
        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));

        // Find and submit the form with empty data
        $form = $crawler->selectButton('Create')->form();
        $crawler = $client->submit($form);

        // Verify that validation errors are properly displayed
        $this->assertResponseStatusCodeSame(422);
        $this->assertStringContainsString('should not be blank', $crawler->filter('.invalid-feedback')->text());
    }
}
