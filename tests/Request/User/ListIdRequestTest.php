<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ListIdRequest;

class ListIdRequestTest extends TestCase
{
    private ListIdRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new ListIdRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(ListIdRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testCursorGetterAndSetter(): void
    {
        $cursor = 'cursor_string_123';
        
        $this->request->setCursor($cursor);
        $this->assertSame($cursor, $this->request->getCursor());
        $this->assertInstanceOf(ListIdRequest::class, $this->request->setCursor($cursor)); // 测试fluent接口
    }
    
    public function testCursorWithNullValue(): void
    {
        $this->request->setCursor(null);
        $this->assertNull($this->request->getCursor());
    }
    
    public function testCursorWithDifferentValues(): void
    {
        $testCases = [
            'first_page_cursor',
            'page_2_cursor_string',
            'very_long_cursor_string_with_special_chars_123456789',
            'cursor-with-dashes',
            'cursor_with_underscores'
        ];
        
        foreach ($testCases as $cursor) {
            $this->request->setCursor($cursor);
            $this->assertSame($cursor, $this->request->getCursor());
        }
    }
    
    public function testLimitGetterAndSetter(): void
    {
        $limit = 500;
        
        $this->request->setLimit($limit);
        $this->assertSame($limit, $this->request->getLimit());
        $this->assertInstanceOf(ListIdRequest::class, $this->request->setLimit($limit)); // 测试fluent接口
    }
    
    public function testLimitWithDifferentValues(): void
    {
        $testCases = [1, 100, 500, 1000, 5000, 10000]; // 有效范围 1-10000
        
        foreach ($testCases as $limit) {
            $this->request->setLimit($limit);
            $this->assertSame($limit, $this->request->getLimit());
        }
    }
    
    public function testLimitDefaultValue(): void
    {
        // 默认值应该是1000
        $this->assertSame(1000, $this->request->getLimit());
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/list_id', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        // ListIdRequest 没有实现getRequestMethod，应该返回null
        $this->assertNull($this->request->getRequestMethod());
    }
    
    public function testGetRequestOptionsWithDefaultValues(): void
    {
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame(1000, $options['json']['limit']);
        $this->assertArrayNotHasKey('cursor', $options['json']); // null cursor 不应该出现
    }
    
    public function testGetRequestOptionsWithCursorOnly(): void
    {
        $cursor = 'test_cursor_page_2';
        $this->request->setCursor($cursor);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame(1000, $options['json']['limit']); // 默认limit
    }
    
    public function testGetRequestOptionsWithAllFields(): void
    {
        $cursor = 'full_test_cursor';
        $limit = 2000;
        
        $this->request->setCursor($cursor);
        $this->request->setLimit($limit);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('cursor', $options['json']);
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame($cursor, $options['json']['cursor']);
        $this->assertSame($limit, $options['json']['limit']);
    }
    
    public function testGetRequestOptionsWithNullCursor(): void
    {
        $limit = 3000;
        
        $this->request->setCursor(null);
        $this->request->setLimit($limit);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayNotHasKey('cursor', $options['json']); // null值不包含
        $this->assertArrayHasKey('limit', $options['json']);
        $this->assertSame($limit, $options['json']['limit']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 