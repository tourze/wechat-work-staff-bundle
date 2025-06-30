<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetTagUsersRequest;

class GetTagUsersRequestTest extends TestCase
{
    private GetTagUsersRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetTagUsersRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetTagUsersRequest::class, $this->request);
    }
}