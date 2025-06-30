<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetUserSimpleListRequest;

class GetUserSimpleListRequestTest extends TestCase
{
    private GetUserSimpleListRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetUserSimpleListRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetUserSimpleListRequest::class, $this->request);
    }
}