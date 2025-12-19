<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatWorkStaffBundle\Param\GetWechatWorkUserByAuthCodeParam;

/**
 * @internal
 */
#[CoversClass(GetWechatWorkUserByAuthCodeParam::class)]
final class GetWechatWorkUserByAuthCodeParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetWechatWorkUserByAuthCodeParam();
        $param->corpId = 'test-corp-id';
        $param->agentId = 'test-agent-id';
        $param->code = 'test-code';

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-corp-id', $param->corpId);
        $this->assertSame('test-agent-id', $param->agentId);
        $this->assertSame('test-code', $param->code);
    }
}
