<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\UpdateUserRequest;

class UpdateUserRequestTest extends TestCase
{
    private UpdateUserRequest $request;

    protected function setUp(): void
    {
        $this->request = new UpdateUserRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(UpdateUserRequest::class, $this->request);
    }
}