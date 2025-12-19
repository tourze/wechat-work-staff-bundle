<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Procedure\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;

/**
 * @internal
 */
#[CoversClass(GetWechatWorkUserByAuthCode::class)]
#[RunTestsInSeparateProcesses]
final class GetWechatWorkUserByAuthCodeTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCanBeInstantiated(): void
    {
        $procedure = self::getService(GetWechatWorkUserByAuthCode::class);
        $this->assertInstanceOf(GetWechatWorkUserByAuthCode::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        // Test that execute method exists and is callable from service container
        $procedure = self::getService(GetWechatWorkUserByAuthCode::class);

        // Verify that execute method exists
        $this->assertTrue(method_exists($procedure, 'execute'), 'execute method should exist');

        // Verify that execute method returns ArrayResult type (based on return type declaration)
        $reflection = new \ReflectionMethod($procedure, 'execute');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType, 'execute method should have return type');

        if ($returnType instanceof \ReflectionNamedType) {
            $this->assertEquals('Tourze\JsonRPC\Core\Result\ArrayResult', $returnType->getName(), 'execute method should return ArrayResult');
        }
    }
}
