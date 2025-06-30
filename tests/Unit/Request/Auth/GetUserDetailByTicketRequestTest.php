<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Auth;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;

class GetUserDetailByTicketRequestTest extends TestCase
{
    private GetUserDetailByTicketRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetUserDetailByTicketRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetUserDetailByTicketRequest::class, $this->request);
    }
}