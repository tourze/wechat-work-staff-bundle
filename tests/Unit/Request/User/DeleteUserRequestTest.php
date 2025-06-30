<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\DeleteUserRequest;

class DeleteUserRequestTest extends TestCase
{
    private DeleteUserRequest $request;

    protected function setUp(): void
    {
        $this->request = new DeleteUserRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(DeleteUserRequest::class, $this->request);
    }
}