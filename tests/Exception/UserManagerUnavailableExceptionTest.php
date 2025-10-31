<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkStaffBundle\Exception\UserManagerUnavailableException;

/**
 * @internal
 */
#[CoversClass(UserManagerUnavailableException::class)]
final class UserManagerUnavailableExceptionTest extends AbstractExceptionTestCase
{
    protected function setUp(): void
    {
        // 无需特殊设置
    }

    public function testExceptionCanBeCreated(): void
    {
        $exception = new UserManagerUnavailableException();

        $this->assertSame('UserManager not available in test environment', $exception->getMessage());
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithCustomMessage(): void
    {
        $customMessage = 'Custom error message';
        $exception = new UserManagerUnavailableException($customMessage);

        $this->assertSame($customMessage, $exception->getMessage());
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionInheritance(): void
    {
        $exception = new UserManagerUnavailableException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }
}
