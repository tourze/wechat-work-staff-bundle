<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ConvertToUserIdRequest;

class ConvertToUserIdRequestTest extends TestCase
{
    private ConvertToUserIdRequest $request;

    protected function setUp(): void
    {
        $this->request = new ConvertToUserIdRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(ConvertToUserIdRequest::class, $this->request);
    }
}