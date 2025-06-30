<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Tag;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Tag\DeleteTagRequest;

class DeleteTagRequestTest extends TestCase
{
    private DeleteTagRequest $request;

    protected function setUp(): void
    {
        $this->request = new DeleteTagRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(DeleteTagRequest::class, $this->request);
    }
}