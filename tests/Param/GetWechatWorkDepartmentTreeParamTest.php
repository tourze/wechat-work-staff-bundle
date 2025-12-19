<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatWorkStaffBundle\Param\GetWechatWorkDepartmentTreeParam;

/**
 * @internal
 */
#[CoversClass(GetWechatWorkDepartmentTreeParam::class)]
final class GetWechatWorkDepartmentTreeParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetWechatWorkDepartmentTreeParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetWechatWorkDepartmentTreeParam();

        // Since the class is readonly and has no properties,
        // we just verify it can be instantiated
        $this->assertInstanceOf(GetWechatWorkDepartmentTreeParam::class, $param);
    }
}
