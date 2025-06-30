<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Unit\Request\Auth;

use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Auth\AuthSuccessConfirmRequest;

class AuthSuccessConfirmRequestTest extends TestCase
{
    private AuthSuccessConfirmRequest $request;

    protected function setUp(): void
    {
        $this->request = new AuthSuccessConfirmRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/user/authsucc', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('GET', $this->request->getRequestMethod());
    }

    public function testSetAndGetUserId(): void
    {
        $userId = 'test_user_id';
        $this->request->setUserId($userId);
        
        $this->assertEquals($userId, $this->request->getUserId());
    }

    public function testGetRequestOptions(): void
    {
        $userId = 'test_user_id';
        $this->request->setUserId($userId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('userid', $options['query']);
        $this->assertEquals($userId, $options['query']['userid']);
    }
}