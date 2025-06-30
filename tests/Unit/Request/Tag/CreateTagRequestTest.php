<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Tag;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\CreateTagRequest;

class CreateTagRequestTest extends TestCase
{
    private CreateTagRequest $request;

    protected function setUp(): void
    {
        $this->request = new CreateTagRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(CreateTagRequest::class, $this->request);
    }
}