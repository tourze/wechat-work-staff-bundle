<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatWorkStaffBundle\DependencyInjection\WechatWorkStaffExtension;

/**
 * @internal
 */
#[CoversClass(WechatWorkStaffExtension::class)]
final class WechatWorkStaffExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
