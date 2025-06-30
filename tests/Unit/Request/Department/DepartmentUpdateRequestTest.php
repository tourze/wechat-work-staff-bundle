<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Department;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Department\DepartmentUpdateRequest;

class DepartmentUpdateRequestTest extends TestCase
{
    private DepartmentUpdateRequest $request;

    protected function setUp(): void
    {
        $this->request = new DepartmentUpdateRequest();
    }

    public function testRequest(): void
    {
        $this->assertInstanceOf(DepartmentUpdateRequest::class, $this->request);
    }
}