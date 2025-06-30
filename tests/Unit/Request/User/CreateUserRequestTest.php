<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\CreateUserRequest;

class CreateUserRequestTest extends TestCase
{
    private CreateUserRequest $request;

    protected function setUp(): void
    {
        $this->request = new CreateUserRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(CreateUserRequest::class, $this->request);
    }
}