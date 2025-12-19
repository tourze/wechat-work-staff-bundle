<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

class GetWechatWorkUserByAuthCodeParam implements RpcParamInterface
{
    #[MethodParam(description: '企业ID')]
    public string $corpId;

    #[MethodParam(description: '应用ID')]
    public string $agentId;

    #[MethodParam(description: '授权Code')]
    public string $code;
}
