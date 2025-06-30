<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Auth;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;

class GetUserInfoByCodeRequestTest extends TestCase
{
    private GetUserInfoByCodeRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetUserInfoByCodeRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetUserInfoByCodeRequest::class, $this->request);
    }
}