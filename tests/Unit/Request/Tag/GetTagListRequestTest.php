<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Tag;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\GetTagListRequest;

class GetTagListRequestTest extends TestCase
{
    private GetTagListRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetTagListRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetTagListRequest::class, $this->request);
    }
}