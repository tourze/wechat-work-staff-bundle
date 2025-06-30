<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Department;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;

class GetDepartmentListRequestTest extends TestCase
{
    private GetDepartmentListRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetDepartmentListRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(GetDepartmentListRequest::class, $this->request);
    }
}