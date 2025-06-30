<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ListIdRequest;

class ListIdRequestTest extends TestCase
{
    private ListIdRequest $request;

    protected function setUp(): void
    {
        $this->request = new ListIdRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(ListIdRequest::class, $this->request);
    }
}