<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

class GetUserRequestTest extends TestCase
{
    private GetUserRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetUserRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetUserRequest::class, $this->request);
    }
}