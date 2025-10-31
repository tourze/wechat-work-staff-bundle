<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkStaffBundle\WechatWorkStaffBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkStaffBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkStaffBundleTest extends AbstractBundleTestCase
{
}
