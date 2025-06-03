<?php

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;

class GetUserDetailByTicketRequestTest extends TestCase
{
    private GetUserDetailByTicketRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserDetailByTicketRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetUserDetailByTicketRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testUserTicketGetterAndSetter(): void
    {
        $userTicket = 'test_user_ticket_123';
        
        $this->request->setUserTicket($userTicket);
        $this->assertSame($userTicket, $this->request->getUserTicket());
    }
    
    public function testUserTicketWithDifferentValues(): void
    {
        $testCases = [
            'short_ticket',
            'very_long_user_ticket_string_with_many_characters_that_can_be_up_to_512_bytes_in_length',
            'ticket-with-dashes',
            'ticket_with_underscores',
            'UPPERCASE_TICKET',
            'MixedCase_Ticket_123'
        ];
        
        foreach ($testCases as $userTicket) {
            $this->request->setUserTicket($userTicket);
            $this->assertSame($userTicket, $this->request->getUserTicket());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/auth/getuserdetail', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('POST', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $userTicket = 'test_ticket_for_request_options';
        $this->request->setUserTicket($userTicket);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('user_ticket', $options['json']);
        $this->assertSame($userTicket, $options['json']['user_ticket']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 