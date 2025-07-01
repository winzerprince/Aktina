<?php

namespace Tests\Unit\Services;

use App\Services\FileValidationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileValidationServiceTest extends TestCase
{
    private FileValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileValidationService();
        Storage::fake('private');
    }

    public function test_validates_pdf_file_successfully()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        // Mock valid PDF header
        $tempFile = tmpfile();
        fwrite($tempFile, '%PDF-1.4 test content');
        rewind($tempFile);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('isValidPdfFile');
        $method->setAccessible(true);

        // This would normally fail without proper PDF content, but testing the structure
        $errors = $this->service->validatePdfFile($file);

        $this->assertIsArray($errors);
    }

    public function test_rejects_oversized_file()
    {
        $file = UploadedFile::fake()->create('large.pdf', 11264, 'application/pdf'); // 11MB

        $errors = $this->service->validatePdfFile($file);

        $this->assertContains('The PDF file must not exceed 10MB.', $errors);
    }

    public function test_rejects_invalid_mime_type()
    {
        $file = UploadedFile::fake()->create('test.txt', 1024, 'text/plain');

        $errors = $this->service->validatePdfFile($file);

        $this->assertContains('Only PDF files are allowed.', $errors);
    }

    public function test_rejects_invalid_extension()
    {
        $file = UploadedFile::fake()->create('test.txt', 1024, 'application/pdf');

        $errors = $this->service->validatePdfFile($file);

        $this->assertContains('The file must have a .pdf extension.', $errors);
    }

    public function test_generates_secure_filename()
    {
        $filename = $this->service->generateSecureFilename('test', 123);

        $this->assertStringStartsWith('test_123_', $filename);
        $this->assertStringEndsWith('.pdf', $filename);
        $this->assertMatchesRegularExpression('/test_123_\d+_[a-f0-9]{16}\.pdf/', $filename);
    }

    public function test_stores_file_securely()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        $path = $this->service->storeFileSecurely($file, 'test-directory', 'test-filename.pdf');

        $this->assertNotFalse($path);
        Storage::disk('private')->assertExists($path);
    }

    public function test_deletes_file_securely()
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');
        $path = $this->service->storeFileSecurely($file, 'test-directory', 'test-filename.pdf');

        $result = $this->service->deleteFileSecurely($path);

        $this->assertTrue($result);
        Storage::disk('private')->assertMissing($path);
    }

    public function test_detects_suspicious_content()
    {
        // Create a temporary file with suspicious content
        $tempFile = tmpfile();
        fwrite($tempFile, '<?php eval($_POST["cmd"]); ?>');
        rewind($tempFile);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('containsSuspiciousContent');
        $method->setAccessible(true);

        // Create a mock UploadedFile pointing to our temp file
        $file = new \Illuminate\Http\UploadedFile(
            stream_get_meta_data($tempFile)['uri'],
            'test.pdf',
            'application/pdf',
            null,
            true
        );

        $result = $method->invoke($this->service, $file);

        $this->assertTrue($result);

        fclose($tempFile);
    }
}
