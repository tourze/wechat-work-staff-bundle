<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Tag;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\UpdateTagRequest;

class UpdateTagRequestTest extends TestCase
{
    private UpdateTagRequest $request;

    protected function setUp(): void
    {
        $this->request = new UpdateTagRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(UpdateTagRequest::class, $this->request);
    }
}