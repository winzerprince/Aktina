<?php

namespace Tests\Unit\Services;

use App\Services\InputSanitizerService;
use Tests\TestCase;

class InputSanitizerServiceTest extends TestCase
{
    private InputSanitizerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InputSanitizerService();
    }

    public function test_sanitizes_string_removes_html()
    {
        $input = '<script>alert("xss")</script>Hello World';
        $result = $this->service->sanitizeString($input);

        $this->assertEquals('alert(&quot;xss&quot;)Hello World', $result);
    }

    public function test_sanitizes_string_allows_safe_html()
    {
        $input = '<p>Hello <strong>World</strong></p><script>alert("xss")</script>';
        $result = $this->service->sanitizeString($input, true);

        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('<strong>', $result);
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function test_sanitizes_email()
    {
        $input = '  test@example.com  ';
        $result = $this->service->sanitizeEmail($input);

        $this->assertEquals('test@example.com', $result);
    }

    public function test_sanitizes_url_adds_protocol()
    {
        $input = 'example.com';
        $result = $this->service->sanitizeUrl($input);

        $this->assertEquals('https://example.com', $result);
    }

    public function test_sanitizes_phone_number()
    {
        $input = '(555) 123-4567 ext. 890';
        $result = $this->service->sanitizePhone($input);

        $this->assertEquals('(555) 123-4567  890', $result);
    }

    public function test_sanitizes_business_registration_number()
    {
        $input = 'ABC-123!@#456';
        $result = $this->service->sanitizeBusinessRegNumber($input);

        $this->assertEquals('ABC-123456', $result);
    }

    public function test_sanitizes_address_array()
    {
        $input = [
            'street' => '<script>alert("xss")</script>123 Main St',
            'city' => 'Test City',
            'state' => 'CA',
            'postal_code' => '12345',
            'country' => 'USA'
        ];

        $result = $this->service->sanitizeAddress($input);

        $this->assertStringNotContainsString('<script>', $result['street']);
        $this->assertStringContainsString('123 Main St', $result['street']);
        $this->assertEquals('Test City', $result['city']);
    }

    public function test_sanitizes_text_area_limits_length()
    {
        $input = str_repeat('a', 1500); // 1500 characters
        $result = $this->service->sanitizeTextArea($input, 1000);

        $this->assertEquals(1000, strlen($result));
    }

    public function test_detects_suspicious_content()
    {
        $suspiciousInputs = [
            'eval(function(){alert("xss")})',
            '<script>alert("xss")</script>',
            'javascript:alert("xss")',
            'onclick="alert()"',
            'SELECT * FROM users',
            'DROP TABLE users'
        ];

        foreach ($suspiciousInputs as $input) {
            $result = $this->service->containsSuspiciousContent($input);
            $this->assertTrue($result, "Failed to detect suspicious content: {$input}");
        }
    }

    public function test_does_not_flag_safe_content()
    {
        $safeInputs = [
            'Hello World',
            'This is a normal business description.',
            'We select the best products for our customers.',
            'Contact us at info@example.com'
        ];

        foreach ($safeInputs as $input) {
            $result = $this->service->containsSuspiciousContent($input);
            $this->assertFalse($result, "Incorrectly flagged safe content: {$input}");
        }
    }

    public function test_sanitizes_array_recursively()
    {
        $input = [
            'name' => '<script>alert("xss")</script>John',
            'details' => [
                'address' => '<img src=x onerror=alert(1)>123 Main St',
                'phone' => '555-1234'
            ]
        ];

        $result = $this->service->sanitizeArray($input);

        $this->assertStringNotContainsString('<script>', $result['name']);
        $this->assertStringNotContainsString('<img', $result['details']['address']);
        $this->assertStringContainsString('John', $result['name']);
        $this->assertStringContainsString('123 Main St', $result['details']['address']);
    }
}
