<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\User;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ConvertToOpenIdRequest;

class ConvertToOpenIdRequestTest extends TestCase
{
    private ConvertToOpenIdRequest $request;

    protected function setUp(): void
    {
        $this->request = new ConvertToOpenIdRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(ConvertToOpenIdRequest::class, $this->request);
    }
}