<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Param;

use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetWechatWorkDepartmentTreeParam implements RpcParamInterface
{
    public function __construct()
    {
    }
}
